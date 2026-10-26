<?php

use App\Models\Article;
use App\Models\User;
use App\Services\OrganizationService;
use App\Support\TenantContext;

it('isolates articles by tenant', function () {
    $user = User::factory()->create();
    $orgA = app(OrganizationService::class)->create($user, ['name' => 'Org A']);
    $orgB = app(OrganizationService::class)->create($user, ['name' => 'Org B']);

    $tenantA = $orgA->tenants()->first();
    $tenantB = $orgB->tenants()->first();

    $context = app(TenantContext::class);
    $context->set($tenantA);
    $article = Article::create([
        'tenant_id' => $tenantA->id,
        'organization_id' => $orgA->id,
        'author_id' => $user->id,
        'title' => 'Secret A',
        'slug' => 'secret-a',
        'body' => 'body',
        'status' => 'draft',
    ]);

    $context->set($tenantB);
    expect(Article::count())->toBe(0);

    $context->set($tenantA);
    expect(Article::count())->toBe(1);
});
