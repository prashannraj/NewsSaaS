<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Support\TenantContext;

class TenantResolver
{
    public function __construct(protected TenantContext $context) {}

    public function resolveByHost(string $host): ?Tenant
    {
        $host = strtolower($host);

        $domain = TenantDomain::where('normalized_domain', $host)->whereNotNull('verified_at')->first();
        if ($domain && $domain->tenant) {
            $this->context->set($domain->tenant);

            return $domain->tenant;
        }

        $centralDomains = array_map('trim', explode(',', (string) config('tenancy.central_domains', 'localhost,127.0.0.1')));
        foreach ($centralDomains as $central) {
            if ($host === $central || str_ends_with($host, '.'.$central)) {
                $subdomain = str_replace('.'.$central, '', $host);
                if ($subdomain !== $host && $subdomain !== '' && $subdomain !== 'www') {
                    $tenant = Tenant::where('subdomain', $subdomain)->first();
                    if ($tenant) {
                        $this->context->set($tenant);

                        return $tenant;
                    }
                }

                return null;
            }
        }

        return null;
    }

    public function clearContext(): void
    {
        $this->context->forget();
    }
}
