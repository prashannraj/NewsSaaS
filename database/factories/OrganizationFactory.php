<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'owner_user_id' => User::factory(),
            'name' => $name,
            'legal_name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(6),
            'subdomain' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'organization_type' => 'media',
            'contact_email' => fake()->safeEmail(),
            'status' => 'active',
            'timezone' => 'UTC',
            'locale' => 'en',
        ];
    }
}
