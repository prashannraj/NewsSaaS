<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $module = fake()->word();

        return [
            'name' => ucfirst($module).' Access',
            'slug' => $module.'.access',
            'module' => $module,
            'description' => fake()->sentence(),
        ];
    }
}
