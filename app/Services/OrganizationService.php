<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\TenantEmailSetting;
use App\Models\TenantNavigationSettings;
use App\Models\TenantSetting;
use App\Models\TenantTheme;
use App\Models\User;
use App\Notifications\WelcomeOrganization;
use Illuminate\Support\Str;

class OrganizationService
{
    public function create(User $owner, array $data): Organization
    {
        return \DB::transaction(function () use ($owner, $data) {
            $organization = Organization::create([
                'owner_user_id' => $owner->id,
                'name' => $data['name'],
                'legal_name' => $data['legal_name'] ?? $data['name'],
                'slug' => Str::slug($data['name']).'-'.Str::random(6),
                'subdomain' => $data['subdomain'] ?? Str::slug($data['name']).'-'.Str::lower(Str::random(4)),
                'organization_type' => $data['organization_type'] ?? 'media',
                'contact_email' => $data['contact_email'] ?? $owner->email,
                'status' => 'active',
                'timezone' => $data['timezone'] ?? 'UTC',
                'locale' => $data['locale'] ?? 'en',
            ]);

            $this->provision($organization, $owner, $data);

            $owner->update(['current_organization_id' => $organization->id]);
            $owner->notify(new WelcomeOrganization);

            return $organization;
        });
    }

    public function provision(Organization $organization, User $owner, array $data = []): void
    {
        $tenant = $organization->tenants()->first();
        if (! $tenant) {
            $tenant = Tenant::create([
                'organization_id' => $organization->id,
                'tenant_key' => Str::slug($organization->name).'-'.Str::lower(Str::random(6)),
                'subdomain' => $organization->subdomain,
                'status' => 'active',
                'provisioned_at' => now(),
                'settings_json' => [],
            ]);
        }

        $membership = $organization->memberships()->where('user_id', $owner->id)->first();
        if (! $membership) {
            $membership = OrganizationMembership::create([
                'organization_id' => $organization->id,
                'user_id' => $owner->id,
                'membership_status' => 'active',
                'joined_at' => now(),
            ]);
        }

        $ownerRole = Role::firstOrCreate(
            ['organization_id' => $organization->id, 'slug' => 'owner'],
            ['name' => 'Owner', 'description' => 'Organization owner', 'is_system' => true]
        );
        $membership->roles()->syncWithoutDetaching([$ownerRole->id]);
        $this->ensureSystemRoles($organization);

        if (! $organization->domains()->where('is_primary', true)->exists()) {
            $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
            TenantDomain::create([
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
                'domain' => $tenant->subdomain.'.'.$host,
                'normalized_domain' => $tenant->subdomain.'.'.$host,
                'domain_type' => 'subdomain',
                'is_primary' => true,
                'verification_method' => 'dns',
                'verified_at' => now(),
                'dns_status' => 'verified',
                'ssl_status' => 'active',
            ]);
        }

        $this->provisionDefaults($organization, $tenant);

        $organization->forceFill(['onboarding_completed_at' => now()])->save();
    }

    protected function ensureSystemRoles(Organization $organization): void
    {
        $permissionMap = [
            'owner' => ['articles.view', 'articles.create', 'articles.update', 'articles.publish', 'articles.delete', 'media.manage', 'categories.manage', 'menus.manage', 'domains.manage', 'billing.view', 'billing.manage', 'members.invite', 'members.manage', 'analytics.view', 'settings.manage', 'support.manage'],
            'admin' => ['articles.view', 'articles.create', 'articles.update', 'articles.publish', 'articles.delete', 'media.manage', 'categories.manage', 'menus.manage', 'domains.manage', 'billing.view', 'billing.manage', 'members.invite', 'members.manage', 'analytics.view', 'settings.manage', 'support.manage'],
            'editor' => ['articles.view', 'articles.create', 'articles.update', 'articles.publish', 'articles.delete', 'media.manage', 'categories.manage', 'menus.manage', 'analytics.view'],
            'journalist' => ['articles.view', 'articles.create', 'articles.update', 'analytics.view'],
            'reporter' => ['articles.view', 'articles.create', 'articles.update'],
            'moderator' => ['articles.view', 'articles.update', 'articles.publish', 'support.manage'],
            'advertiser' => ['analytics.view'],
            'analyst' => ['articles.view', 'analytics.view'],
        ];

        $names = [
            'owner' => 'Owner',
            'admin' => 'Organization Admin',
            'editor' => 'Editor',
            'journalist' => 'Journalist',
            'reporter' => 'Reporter',
            'moderator' => 'Moderator',
            'advertiser' => 'Advertiser',
            'analyst' => 'Read-only Analyst',
        ];

        foreach ($permissionMap as $slug => $slugs) {
            $role = Role::firstOrCreate(
                ['organization_id' => $organization->id, 'slug' => $slug],
                ['name' => $names[$slug], 'description' => $names[$slug].' role', 'is_system' => true]
            );

            $permissionIds = Permission::whereIn('slug', $slugs)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }

    protected function provisionDefaults(Organization $organization, Tenant $tenant): void
    {
        if (! TenantSetting::where('tenant_id', $tenant->id)->where('key', 'seo_defaults')->exists()) {
            TenantSetting::create([
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
                'key' => 'seo_defaults',
                'value' => [
                    'site_title' => $organization->name,
                    'meta_description' => 'Latest news from '.$organization->name,
                    'robots' => 'index,follow',
                ],
            ]);
        }

        if (! TenantTheme::where('tenant_id', $tenant->id)->exists()) {
            TenantTheme::create([
                'tenant_id' => $tenant->id,
                'name' => 'Default',
                'settings' => ['primary_color' => '#111827', 'font' => 'Inter'],
                'is_active' => true,
            ]);
        }

        if (! TenantNavigationSettings::where('tenant_id', $tenant->id)->exists()) {
            TenantNavigationSettings::create([
                'tenant_id' => $tenant->id,
                'settings' => ['header' => [], 'footer' => []],
            ]);
        }

        if (! TenantEmailSetting::where('tenant_id', $tenant->id)->exists()) {
            TenantEmailSetting::create([
                'tenant_id' => $tenant->id,
                'mailer' => 'smtp',
                'from_name' => $organization->name,
                'settings' => [],
            ]);
        }

        if ($organization->categories()->count() === 0) {
            foreach (['News', 'Politics', 'Business', 'Technology'] as $name) {
                Category::create([
                    'tenant_id' => $tenant->id,
                    'organization_id' => $organization->id,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'is_active' => true,
                ]);
            }
        }

        if (! Menu::where('tenant_id', $tenant->id)->where('location', 'header')->exists()) {
            Menu::create([
                'tenant_id' => $tenant->id,
                'name' => 'Header',
                'location' => 'header',
            ]);
        }

        if (! Page::where('tenant_id', $tenant->id)->where('slug', 'about')->exists()) {
            Page::create([
                'tenant_id' => $tenant->id,
                'title' => 'About',
                'slug' => 'about',
                'content' => 'About '.$organization->name,
                'status' => 'published',
                'published_at' => now(),
            ]);
        }
    }
}
