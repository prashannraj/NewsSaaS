<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'code' => Str::slug($name).'-'.Str::random(4),
            'slug' => Str::slug($name).'-'.Str::random(4),
            'name' => $name,
            'description' => fake()->sentence(),
            'type' => 'subscription',
            'billing_interval' => 'monthly',
            'interval_count' => 1,
            'pricing_model' => 'flat',
            'price_cents' => fake()->randomElement([0, 1000, 2500, 5000]),
            'currency' => 'USD',
            'trial_days' => 14,
            'seat_limit' => 5,
            'article_limit' => 100,
            'storage_limit_bytes' => 1073741824,
            'domain_limit' => 1,
            'is_active' => true,
            'status' => 'active',
            'is_public' => true,
            'sort_order' => 0,
        ];
    }
}
