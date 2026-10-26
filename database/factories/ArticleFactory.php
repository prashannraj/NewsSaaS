<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $tenant = Tenant::factory()->create();
        $title = fake()->sentence();

        return [
            'tenant_id' => $tenant->id,
            'organization_id' => $tenant->organization_id,
            'author_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(4),
            'excerpt' => fake()->sentence(),
            'body' => fake()->paragraphs(3, true),
            'status' => 'draft',
            'visibility' => 'public',
            'language_code' => 'en',
            'allow_indexing' => true,
        ];
    }
}
