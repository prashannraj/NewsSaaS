<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\ThemeRegistry;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    protected array $allowedPages = ['about', 'contact', 'privacy', 'terms'];

    public function show(string $page, Request $request, TenantContext $context, ThemeRegistry $themes): View
    {
        if (! in_array($page, $this->allowedPages)) {
            abort(404);
        }

        $tenant = $context->get();
        $theme = $themes->resolve($tenant?->theme, $tenant?->theme?->directory);

        $content = \App\Models\Page::where('tenant_id', $tenant->id)
            ->where('slug', $page)
            ->where('status', 'published')
            ->first();

        return view('public.static.'.$page, compact('tenant', 'theme', 'content'));
    }

    public function contact(Request $request, TenantContext $context, ThemeRegistry $themes): View
    {
        $tenant = $context->get();
        $theme = $themes->resolve($tenant?->theme, $tenant?->theme?->directory);

        return view('public.static.contact', compact('tenant', 'theme'));
    }

    public function submitContact(Request $request, TenantContext $context)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        \App\Jobs\SendContactFormNotification::dispatch($context->get()->id, $validated);

        return back()->with('status', 'contact-sent');
    }
}