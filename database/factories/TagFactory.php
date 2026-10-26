<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        $tenant = Tenant::factory()->create();
        $name = fake()->word();

        return [
            'tenant_id' => $tenant->id,
            'organization_id' => $tenant->organization_id,
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
