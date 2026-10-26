<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ThemeRegistry;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function show(string $slug, Request $request, TenantContext $context, ThemeRegistry $themes): View
    {
        $tenant = $context->get();
        $theme = $themes->resolve($tenant?->theme, $tenant?->theme?->directory);
        $version = $tenant->setting('content_version', 1);

        $cacheKey = 'article:'.$tenant->id.':'.$slug.':v'.$version.':user'.($request->user() ? $request->user()->id : 'guest');

        $article = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($tenant, $slug) {
            return Article::published()
                ->where('tenant_id', $tenant->id)
                ->where('slug', $slug)
                ->with(['author', 'category', 'tags', 'featuredImage'])
                ->firstOrFail();
        });

        $this->recordView($article, $request->user());

        return view('public.article.show', compact('tenant', 'theme', 'article'));
    }

    protected function recordView(Article $article, $user = null): void
    {
        Cache::push('article_view_queue', $article->id, 60);
    }
}