<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['key' => 'max_articles', 'name' => 'Max Articles', 'type' => 'integer'],
            ['key' => 'max_storage_bytes', 'name' => 'Max Storage Bytes', 'type' => 'integer'],
            ['key' => 'max_domains', 'name' => 'Max Domains', 'type' => 'integer'],
            ['key' => 'custom_domain', 'name' => 'Custom Domain', 'type' => 'boolean'],
            ['key' => 'priority_support', 'name' => 'Priority Support', 'type' => 'boolean'],
        ];

        foreach ($features as $feature) {
            Feature::firstOrCreate(['key' => $feature['key']], $feature);
        }

        $plans = [
            [
                'code' => 'free',
                'name' => 'Free',
                'type' => 'subscription',
                'billing_interval' => 'monthly',
                'price_cents' => 0,
                'currency' => 'USD',
                'trial_days' => 0,
                'seat_limit' => 1,
                'article_limit' => 10,
                'storage_limit_bytes' => 104857600,
                'domain_limit' => 1,
                'is_public' => true,
                'sort_order' => 1,
                'features' => [
                    'max_articles' => 10,
                    'max_storage_bytes' => 104857600,
                    'max_domains' => 1,
                    'custom_domain' => false,
                    'priority_support' => false,
                ],
            ],
            [
                'code' => 'pro-monthly',
                'name' => 'Pro',
                'type' => 'subscription',
                'billing_interval' => 'monthly',
                'price_cents' => 2900,
                'currency' => 'USD',
                'trial_days' => 14,
                'seat_limit' => 10,
                'article_limit' => 1000,
                'storage_limit_bytes' => 10737418240,
                'domain_limit' => 3,
                'is_public' => true,
                'sort_order' => 2,
                'features' => [
                    'max_articles' => 1000,
                    'max_storage_bytes' => 10737418240,
                    'max_domains' => 3,
                    'custom_domain' => true,
                    'priority_support' => true,
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $featureValues = $planData['features'];
            unset($planData['features']);

            $plan = Plan::updateOrCreate(['code' => $planData['code']], $planData);

            foreach ($featureValues as $key => $value) {
                $feature = Feature::where('key', $key)->first();
                if ($feature) {
                    $plan->features()->syncWithoutDetaching([
                        $feature->id => ['value' => (string) $value],
                    ]);
                }
            }

            $plan->update(['features' => $featureValues]);
        }
    }
}
