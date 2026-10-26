<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;

class OrganizationPolicy
{
    public function view(User $user, Organization $organization): bool
    {
        return $user->organizations()->where('organizations.id', $organization->id)->exists() || $user->isPlatformAdmin();
    }

    public function update(User $user, Organization $organization): bool
    {
        $role = $this->role($user, $organization);

        return in_array($role, ['owner', 'admin'], true) || $user->isPlatformAdmin();
    }

    public function delete(User $user, Organization $organization): bool
    {
        $role = $this->role($user, $organization);

        return $role === 'owner' || $user->isPlatformAdmin();
    }

    protected function role(User $user, Organization $organization): ?string
    {
        $membership = OrganizationMembership::where('organization_id', $organization->id)
            ->where('user_id', $user->id)
            ->where('membership_status', 'active')
            ->first();

        if (! $membership) {
            return null;
        }

        return $membership->roles()->pluck('slug')->first();
    }

    public function inviteMembers(User $user, Organization $organization): bool
    {
        $role = $this->role($user, $organization);

        return in_array($role, ['owner', 'admin'], true) || $user->isPlatformAdmin();
    }

    public function manage(User $user, Organization $organization): bool
    {
        return $this->inviteMembers($user, $organization);
    }
}
