<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $name = fake()->word();

        return [
            'organization_id' => Organization::factory(),
            'tenant_key' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'subdomain' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'status' => 'active',
            'provisioned_at' => now(),
            'settings_json' => [],
        ];
    }
}
