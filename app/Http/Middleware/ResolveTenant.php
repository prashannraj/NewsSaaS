<?php

namespace App\Http\Middleware;

use App\Services\TenantResolver;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(protected TenantResolver $resolver, protected TenantContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $tenant = $this->resolver->resolveByHost($host);

        if ($tenant) {
            $request->attributes->set('tenant', $tenant);
            $this->context->set($tenant);
        }

        $response = $next($request);

        $this->context->forget();

        return $response;
    }
}