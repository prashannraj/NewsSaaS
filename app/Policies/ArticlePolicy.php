<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\OrganizationMembership;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Article $article): bool
    {
        return $article->organization_id === $user->current_organization_id || $user->isPlatformAdmin();
    }

    public function create(User $user): bool
    {
        $role = $this->role($user, $user->current_organization_id);

        return in_array($role, ['owner', 'admin', 'editor', 'journalist', 'reporter'], true) || $user->isPlatformAdmin();
    }

    public function update(User $user, Article $article): bool
    {
        $role = $this->role($user, $article->organization_id);

        return in_array($role, ['owner', 'admin', 'editor'], true)
            || in_array($role, ['journalist', 'reporter'], true) && $article->author_id === $user->id
            || $user->isPlatformAdmin();
    }

    public function delete(User $user, Article $article): bool
    {
        $role = $this->role($user, $article->organization_id);

        return in_array($role, ['owner', 'admin'], true) || $user->isPlatformAdmin();
    }

    protected function role(User $user, ?int $organizationId): ?string
    {
        if (! $organizationId) {
            return null;
        }

        $membership = OrganizationMembership::where('organization_id', $organizationId)
            ->where('user_id', $user->id)
            ->where('membership_status', 'active')
            ->first();

        if (! $membership) {
            return null;
        }

        return $membership->roles()->pluck('slug')->first();
    }
}
