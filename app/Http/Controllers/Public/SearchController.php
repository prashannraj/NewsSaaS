<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ThemeRegistry;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request, TenantContext $context, ThemeRegistry $themes): View
    {
        $tenant = $context->get();
        $theme = $themes->resolve($tenant?->theme, $tenant?->theme?->directory);

        $query = trim($request->query('q', ''));
        $version = $tenant->setting('content_version', 1);
        $perPage = (int) $tenant->setting('articles_per_page', 10);

        if (empty($query)) {
            return view('public.search.index', compact('tenant', 'theme', 'query', 'articles'));
        }

        $cacheKey = 'search:'.$tenant->id.':'.md5($query).':page'.$request->query('page', 1).':v'.$version;

        $articles = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($tenant, $query, $perPage) {
            return Article::published()
                ->where('tenant_id', $tenant->id)
                ->where(fn ($q) => $q->where('title', 'LIKE', "%{$query}%")->orWhere('body', 'LIKE', "%{$query}%")->orWhere('excerpt', 'LIKE', "%{$query}%"))
                ->latest('published_at')
                ->paginate($perPage);
        });

        return view('public.search.index', compact('tenant', 'theme', 'query', 'articles'));
    }
}