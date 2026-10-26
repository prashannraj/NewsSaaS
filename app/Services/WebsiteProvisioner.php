<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\TenantNavigationSettings;
use App\Models\TenantSetting;
use App\Models\TenantSocialLink;
use App\Models\TenantTheme;
use App\Support\Theme\ThemeConfig;
use App\Support\Theme\ThemeRegistry;
use Illuminate\Support\Facades\DB;

class WebsiteProvisioner
{
    public function __construct(protected ThemeRegistry $themes)
    {
    }

    public function provision(Tenant $tenant): void
    {
        DB::transaction(function () use ($tenant) {
            $this->createDefaultTheme($tenant);
            $this->createDefaultSettings($tenant);
            $this->createDefaultNavigation($tenant);
            $this->createDefaultDomain($tenant);
        });
    }

    protected function createDefaultTheme(Tenant $tenant): void
    {
        TenantTheme::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $tenant->organization_id,
            'name' => 'Modern News',
            'directory' => 'modern-news',
            'settings' => [],
            'is_active' => true,
        ]);
    }

    protected function createDefaultSettings(Tenant $tenant): void
    {
        $defaults = [
            ['key' => 'site_name', 'value' => 'My News Site'],
            ['key' => 'site_description', 'value' => 'Stay informed with the latest news'],
            ['key' => 'articles_per_page', 'value' => 10],
            ['key' => 'homepage_featured_limit', 'value' => 5],
            ['key' => 'homepage_latest_limit', 'value' => 10],
            ['key' => 'content_version', 'value' => 1],
            ['key' => 'enable_rss', 'value' => true],
            ['key' => 'enable_sitemap', 'value' => true],
            ['key' => 'allow_comments', 'value' => true],
            ['key' => 'timezone', 'value' => 'UTC'],
        ];

        foreach ($defaults as $setting) {
            TenantSetting::create([
                'tenant_id' => $tenant->id,
                'organization_id' => $tenant->organization_id,
                'key' => $setting['key'],
                'value' => $setting['value'],
            ]);
        }
    }

    protected function createDefaultNavigation(Tenant $tenant): void
    {
        $nav = TenantNavigationSettings::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $tenant->organization_id,
            'settings' => [
                'menus' => [
                    'primary' => [
                        ['label' => 'Home', 'url' => '/', 'type' => 'route', 'route_name' => 'public.home'],
                        ['label' => 'News', 'url' => '/news', 'type' => 'route', 'route_name' => 'public.articles.index'],
                        ['label' => 'Contact', 'url' => '/contact', 'type' => 'route', 'route_name' => 'public.contact'],
                    ],
                ],
            ],
        ]);
    }

    protected function createDefaultDomain(Tenant $tenant): void
    {
        if (! $tenant->primaryDomain()->exists) {
            $subdomain = $tenant->subdomain ?? $tenant->organization->subdomain ?? 'site';
            $centralDomains = config('tenancy.central_domains', 'localhost');
            $suffix = config('tenancy.subdomain_suffix', '.localhost');
            $domain = $subdomain.$suffix;

            $d = new TenantDomain([
                'tenant_id' => $tenant->id,
                'organization_id' => $tenant->organization_id,
                'domain' => $domain,
                'normalized_domain' => strtolower($domain),
                'domain_type' => 'subdomain',
                'is_primary' => true,
                'verification_method' => 'dns',
                'type' => 'primary',
                'failure_reason' => null,
                'dns_status' => 'pending',
                'ssl_status' => 'pending',
                'last_checked_at' => now(),
            ]);

            // Set verified_at for seeded/demo domains
            if ($subdomain === 'demo') {
                $d->verified_at = now();
                $d->verification_token = null;
                $d->verification_token_hash = null;
            }

            $tenant->primaryDomain()->save($d);
        }
    }
}
