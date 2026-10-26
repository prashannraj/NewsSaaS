<?php

namespace App\Services;

use App\Models\Entitlement;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Str;

class BillingService
{
    public function subscribe(Organization $organization, Plan $plan, string $provider = 'manual'): Subscription
    {
        return \DB::transaction(function () use ($organization, $plan, $provider) {
            $subscription = Subscription::create([
                'organization_id' => $organization->id,
                'plan_id' => $plan->id,
                'provider' => $provider,
                'billing_provider' => $provider,
                'status' => 'active',
                'quantity' => 1,
                'currency' => $plan->currency,
                'unit_amount' => $plan->price_cents,
                'trial_ends_at' => $plan->trial_days ? now()->addDays($plan->trial_days) : null,
                'current_period_starts_at' => now(),
                'current_period_ends_at' => $plan->billing_interval === 'monthly' ? now()->addMonth() : ($plan->billing_interval === 'yearly' ? now()->addYear() : null),
                'renews_at' => $plan->billing_interval === 'monthly' ? now()->addMonth() : ($plan->billing_interval === 'yearly' ? now()->addYear() : null),
            ]);

            $invoice = Invoice::create([
                'organization_id' => $organization->id,
                'subscription_id' => $subscription->id,
                'number' => 'INV-'.strtoupper(Str::random(10)),
                'amount_cents' => $plan->price_cents,
                'currency' => $plan->currency,
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            Payment::create([
                'organization_id' => $organization->id,
                'payable_type' => Invoice::class,
                'payable_id' => $invoice->id,
                'provider' => $provider,
                'amount_cents' => $plan->price_cents,
                'currency' => $plan->currency,
                'status' => 'succeeded',
            ]);

            $assignments = $plan->features()->get();
            if ($assignments->isNotEmpty()) {
                foreach ($assignments as $feature) {
                    Entitlement::updateOrCreate(
                        ['organization_id' => $organization->id, 'key' => $feature->key],
                        ['value' => (string) ($feature->pivot->value ?? 'true'), 'source' => 'plan:'.$plan->code]
                    );
                }
            } elseif (is_array($plan->features)) {
                foreach ($plan->features as $key => $value) {
                    Entitlement::updateOrCreate(
                        ['organization_id' => $organization->id, 'key' => $key],
                        ['value' => is_scalar($value) ? (string) $value : json_encode($value), 'source' => 'plan:'.$plan->code]
                    );
                }
            }

            return $subscription;
        });
    }
}
