<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Support\TenantContext;
use App\Services\ThemeRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TagController extends Controller
{
    public function show(Request $request, string $slug, TenantContext $context, ThemeRegistry $themes): View
    {
        $tenant = $context->get();
        $theme = $themes->resolve($tenant?->theme, $tenant?->theme?->directory);
        $version = $tenant->setting('content_version', 1);

        $tag = Cache::remember(
            'tag:'.$tenant->id.':'.$slug.':v'.$version,
            now()->addMinutes(10),
            fn () => Tag::where('tenant_id', $tenant->id)->where('slug', $slug)->firstOrFail()
        );

        $perPage = (int) $tenant->setting('articles_per_page', 10);

        $articles = Cache::remember(
            'tag_articles:'.$tenant->id.':'.$slug.':page'.$request->query('page', 1).':v'.$version,
            now()->addMinutes(5),
            fn () => \App\Models\Article::published()
                ->where('tenant_id', $tenant->id)
                ->whereHas('tags', fn ($q) => $q->where('tags.id', $tag->id))
                ->latest('published_at')
                ->paginate($perPage)
        );

        return view('public.tag.index', compact('tenant', 'theme', 'tag', 'articles'));
    }
}