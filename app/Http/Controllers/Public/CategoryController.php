<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Services\ThemeRegistry;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Request $request, string $slug, TenantContext $context, ThemeRegistry $themes): View
    {
        $tenant = $context->get();
        $theme = $themes->resolve($tenant?->theme, $tenant?->theme?->directory);
        $version = $tenant->setting('content_version', 1);

        $category = Cache::remember(
            'category:'.$tenant->id.':'.$slug.':v'.$version,
            now()->addMinutes(10),
            fn () => Category::where('tenant_id', $tenant->id)->where('slug', $slug)->where('is_active', true)->firstOrFail()
        );

        $perPage = (int) $tenant->setting('articles_per_page', 10);

        $articles = Cache::remember(
            'category_articles:'.$tenant->id.':'.$slug.':page'.$request->query('page', 1).':v'.$version,
            now()->addMinutes(5),
            fn () => Article::published()
                ->where('tenant_id', $tenant->id)
                ->where('category_id', $category->id)
                ->latest('published_at')
                ->paginate($perPage)
        );

        return view('public.category.index', compact('tenant', 'theme', 'category', 'articles'));
    }
}