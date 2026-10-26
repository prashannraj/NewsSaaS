<?php

use App\Models\Invitation;
use App\Models\OrganizationMembership;
use App\Models\User;
use App\Services\InvitationService;
use App\Services\OrganizationService;

it('prevents duplicate membership invitations', function () {
    $owner = User::factory()->create();
    $organization = app(OrganizationService::class)->create($owner, ['name' => 'Invite Org']);

    $invitee = User::factory()->create(['email' => 'invitee@example.com']);
    $membership = OrganizationMembership::create([
        'organization_id' => $organization->id,
        'user_id' => $invitee->id,
        'membership_status' => 'active',
        'joined_at' => now(),
    ]);

    expect(fn () => app(InvitationService::class)->invite(
        $organization,
        'invitee@example.com',
        $organization->roles()->where('slug', 'editor')->first(),
        $owner
    ))->toThrow(RuntimeException::class);
});

it('accepts a valid invitation', function () {
    $owner = User::factory()->create();
    $organization = app(OrganizationService::class)->create($owner, ['name' => 'Invite Org 2']);

    $rawToken = 'valid-token';
    Invitation::create([
        'organization_id' => $organization->id,
        'email' => 'newuser@example.com',
        'token_hash' => hash('sha256', $rawToken),
        'role_id' => $organization->roles()->where('slug', 'editor')->first()->id,
        'invited_by' => $owner->id,
        'expires_at' => now()->addDay(),
    ]);

    $user = User::factory()->create(['email' => 'newuser@example.com']);
    $membership = app(InvitationService::class)->accept($rawToken, $user);

    expect($membership->organization_id)->toBe($organization->id);
    expect($organization->memberships()->where('user_id', $user->id)->exists())->toBeTrue();
});

it('rejects expired invitations', function () {
    $owner = User::factory()->create();
    $organization = app(OrganizationService::class)->create($owner, ['name' => 'Invite Org 3']);

    $rawToken = 'expired-token';
    Invitation::create([
        'organization_id' => $organization->id,
        'email' => 'expired@example.com',
        'token_hash' => hash('sha256', $rawToken),
        'invited_by' => $owner->id,
        'expires_at' => now()->subDay(),
    ]);

    $user = User::factory()->create(['email' => 'expired@example.com']);

    expect(fn () => app(InvitationService::class)->accept($rawToken, $user))
        ->toThrow(RuntimeException::class);
});

it('rejects invitation replay', function () {
    $owner = User::factory()->create();
    $organization = app(OrganizationService::class)->create($owner, ['name' => 'Invite Org 4']);

    $rawToken = 'replay-token';
    $invitation = Invitation::create([
        'organization_id' => $organization->id,
        'email' => 'replay@example.com',
        'token_hash' => hash('sha256', $rawToken),
        'invited_by' => $owner->id,
        'expires_at' => now()->addDay(),
        'accepted_at' => now(),
    ]);

    $user = User::factory()->create(['email' => 'replay@example.com']);

    expect(fn () => app(InvitationService::class)->accept($rawToken, $user))
        ->toThrow(RuntimeException::class);
});
