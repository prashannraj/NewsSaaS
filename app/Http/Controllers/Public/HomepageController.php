<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Article;
use App\Models\Category;
use App\Services\ThemeRegistry;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomepageController extends Controller
{
    public function __invoke(Request $request, TenantContext $context, ThemeRegistry $themes): View
    {
        $tenant = $context->get();
        $theme = $themes->resolve($tenant?->theme, $tenant?->theme?->directory);

        $version = $tenant->setting('content_version', 1);
        $featuredLimit = (int) $tenant->setting('homepage_featured_limit', 5);

        $articles = Cache::remember(
            'homepage_featured:'.$tenant->id.':v'.$version,
            now()->addMinutes(5),
            fn () => Article::published()
                ->where('tenant_id', $tenant->id)
                ->latest('published_at')
                ->limit($featuredLimit)
                ->get()
        );

        $categories = Cache::remember(
            'homepage_categories:'.$tenant->id.':v'.$version,
            now()->addMinutes(10),
            fn () => Category::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->withCount('articles')
                ->has('articles')
                ->limit(6)
                ->get()
        );

        $latest = Cache::remember(
            'homepage_latest:'.$tenant->id.':v'.$version,
            now()->addMinutes(5),
            fn () => Article::published()
                ->where('tenant_id', $tenant->id)
                ->latest('published_at')
                ->limit(10)
                ->get()
        );

        return view('public.home.index', compact('tenant', 'theme', 'articles', 'categories', 'latest'));
    }
}