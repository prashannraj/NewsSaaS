<?php

use App\Models\Plan;
use App\Models\User;
use App\Services\BillingService;
use App\Services\OrganizationService;

it('creates subscription and entitlements on billing', function () {
    $user = User::factory()->create();
    $organization = app(OrganizationService::class)->create($user, ['name' => 'Billing Org']);

    $plan = Plan::create([
        'code' => 'pro-monthly',
        'name' => 'Pro',
        'type' => 'subscription',
        'billing_interval' => 'monthly',
        'price_cents' => 1000,
        'currency' => 'USD',
        'features' => ['max_articles' => 100],
        'is_active' => true,
    ]);

    $subscription = app(BillingService::class)->subscribe($organization, $plan);

    expect($subscription->status)->toBe('active');
    expect($organization->subscriptions()->count())->toBe(1);
    expect($organization->entitlements()->where('key', 'max_articles')->exists())->toBeTrue();
    expect($organization->invoices()->count())->toBe(1);
});
