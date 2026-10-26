<?php

use App\Models\Article;
use App\Models\OrganizationMembership;
use App\Models\User;
use App\Services\OrganizationService;

it('prevents role escalation for read-only analysts', function () {
    $owner = User::factory()->create();
    $organization = app(OrganizationService::class)->create($owner, ['name' => 'RBAC Org']);

    $analyst = User::factory()->create();
    $membership = OrganizationMembership::create([
        'organization_id' => $organization->id,
        'user_id' => $analyst->id,
        'membership_status' => 'active',
        'joined_at' => now(),
    ]);
    $membership->roles()->sync(
        $organization->roles()->where('slug', 'analyst')->pluck('id')
    );
    $analyst->update(['current_organization_id' => $organization->id]);

    expect($analyst->can('create', Article::class))->toBeFalse();
    expect($owner->can('create', Article::class))->toBeTrue();
});

it('isolates articles between tenants', function () {
    $ownerA = User::factory()->create();
    $orgA = app(OrganizationService::class)->create($ownerA, ['name' => 'Org A']);
    $tenantA = $orgA->tenants()->first();

    $ownerB = User::factory()->create();
    $orgB = app(OrganizationService::class)->create($ownerB, ['name' => 'Org B']);

    $article = Article::create([
        'organization_id' => $orgA->id,
        'tenant_id' => $tenantA->id,
        'title' => 'Secret',
        'body' => 'Body',
        'status' => 'draft',
        'author_id' => $ownerA->id,
        'slug' => 'secret',
    ]);

    $ownerB->update(['current_organization_id' => $orgB->id]);

    expect($ownerB->can('view', $article))->toBeFalse();
});
