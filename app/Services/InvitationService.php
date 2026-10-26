<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Role;
use App\Models\User;
use App\Notifications\OrganizationInvitation;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Str;

class InvitationService
{
    public function invite(Organization $organization, string $email, ?Role $role, User $inviter): Invitation
    {
        $email = strtolower(trim($email));

        if ($this->isMember($organization, $email)) {
            throw new \RuntimeException('User is already a member of this organization.');
        }

        $this->revokePending($organization, $email);

        $token = Str::random(64);
        $invitation = Invitation::create([
            'organization_id' => $organization->id,
            'email' => $email,
            'token_hash' => hash('sha256', $token),
            'role_id' => $role?->id,
            'invited_by' => $inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        $notifiable = new AnonymousNotifiable;
        $notifiable->route('mail', $email);
        $notifiable->notify(new OrganizationInvitation($organization, $token, $inviter));

        $this->audit($organization, $inviter, 'invitation.created', $invitation);

        return $invitation;
    }

    public function accept(string $rawToken, User $user): OrganizationMembership
    {
        $invitation = Invitation::where('token_hash', hash('sha256', $rawToken))->first();

        if (! $invitation || $invitation->accepted_at || $invitation->revoked_at) {
            throw new \RuntimeException('Invalid invitation token.');
        }

        if ($invitation->expires_at->isPast()) {
            throw new \RuntimeException('Invitation has expired.');
        }

        if (strtolower($invitation->email) !== strtolower($user->email)) {
            throw new \RuntimeException('Invitation email does not match authenticated user.');
        }

        $organization = $invitation->organization;
        if (! $organization || $organization->status !== 'active') {
            throw new \RuntimeException('Organization is not active.');
        }

        if ($this->isMember($organization, $user->email)) {
            throw new \RuntimeException('User is already a member.');
        }

        $membership = OrganizationMembership::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'membership_status' => 'active',
            'joined_at' => now(),
            'invited_by' => $invitation->invited_by,
        ]);

        if ($invitation->role_id) {
            $membership->roles()->syncWithoutDetaching([$invitation->role_id]);
        }

        $invitation->forceFill(['accepted_at' => now()])->save();

        $user->update(['current_organization_id' => $organization->id]);

        $this->audit($organization, $user, 'invitation.accepted', $invitation);

        return $membership;
    }

    public function revoke(Invitation $invitation, User $actor): void
    {
        $invitation->forceFill(['revoked_at' => now()])->save();
        $this->audit($invitation->organization, $actor, 'invitation.revoked', $invitation);
    }

    public function resend(Invitation $invitation, User $actor): Invitation
    {
        $token = Str::random(64);
        $invitation->forceFill([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDays(7),
            'revoked_at' => null,
        ])->save();

        $notifiable = new AnonymousNotifiable;
        $notifiable->route('mail', $invitation->email);
        $notifiable->notify(new OrganizationInvitation($invitation->organization, $token, $actor));

        $this->audit($invitation->organization, $actor, 'invitation.resent', $invitation);

        return $invitation;
    }

    protected function isMember(Organization $organization, string $email): bool
    {
        return $organization->memberships()->whereHas('user', fn ($q) => $q->where('email', $email))->exists();
    }

    protected function revokePending(Organization $organization, string $email): void
    {
        Invitation::where('organization_id', $organization->id)
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }

    protected function audit(Organization $organization, User $actor, string $event, Invitation $invitation): void
    {
        AuditLog::create([
            'organization_id' => $organization->id,
            'actor_user_id' => $actor->id,
            'event' => $event,
            'subject_type' => Invitation::class,
            'subject_id' => $invitation->id,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'metadata_json' => ['email' => $invitation->email],
            'created_at' => now(),
        ]);
    }
}
