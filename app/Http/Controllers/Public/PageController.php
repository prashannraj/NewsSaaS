<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\ThemeRegistry;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug, TenantContext $context, ThemeRegistry $themes): View
    {
        $tenant = $context->get();
        $theme = $themes->resolve($tenant?->theme, $tenant?->theme?->directory);

        $page = Cache::remember(
            'page:'.$tenant->id.':'.$slug.':v'.$tenant->setting('content_version', 1),
            now()->addMinutes(10),
            fn () => Page::where('tenant_id', $tenant->id)
                ->where('slug', $slug)
                ->where('status', 'published')
                ->firstOrFail()
        );

        return view('public.page.show', compact('tenant', 'theme', 'page'));
    }
}