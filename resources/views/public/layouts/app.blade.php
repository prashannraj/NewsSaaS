<!DOCTYPE html>
<html lang="en" {{ $tenant->settings()->where('key', 'language_code')->value('value') ? 'lang='.$tenant->settings()->where('key', 'language_code')->value('value') : 'lang=en' }}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $seo = app(\App\Services\SeoManager::class);
        $version = $tenant->settings()->where('key', 'content_version')->value('value') ?? 1;
    @endphp

    <title>{{ $seo->title($seoType ?? 'home', $seoModel ?? null, $tenant) }}</title>
    <meta name="description" content="{{ $seo->description($seoType ?? 'home', $seoModel ?? null, $tenant) }}">
    <link rel="canonical" href="{{ $seo->canonicalUrl($seoType ?? 'home', $seoModel ?? null, $tenant) }}">

    @if ($tenant->settings()->where('key', 'enable_rss')->value('value') ?? true)
        <link rel="alternate" type="application/rss+xml" title="{{ $tenant->settings()->where('key', 'site_name')->value('value') ?? 'News Site' }} RSS" href="{{ $seo->canonicalUrl('home', null, $tenant).'/feed/rss' }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $seo->title($seoType ?? 'home', $seoModel ?? null, $tenant) }}">
    <meta property="og:description" content="{{ $seo->description($seoType ?? 'home', $seoModel ?? null, $tenant) }}">
    <meta property="og:url" content="{{ $seo->canonicalUrl($seoType ?? 'home', $seoModel ?? null, $tenant) }}">
    <meta property="og:type" content="{{ $seoType === 'article' ? 'article' : 'website' }}">
    <meta property="og:site_name" content="{{ $tenant->settings()->where('key', 'site_name')->value('value') ?? 'News Site' }}">

    @if (isset($theme) && $theme->primaryColor())
        <meta name="theme-color" content="{{ $theme->primaryColor() }}">
    @endif

    {{-- JSON-LD --}}
    <script type="application/ld+json">
        {!! json_encode($seo->jsonLd($seoType ?? 'home', $seoModel ?? null, $tenant)) !!}
    </script>

    {{-- Theme CSS --}}
    @if ($theme && $theme->customCss())
        <style>{{ $theme->customCss() }}</style>
    @endif

    {{-- Theme CSS variables --}}
    @if ($theme && $theme->cssVariables())
        :root {
            @foreach ($theme->cssVariables() as $var => $val)
                {{ $var }}: {{ $val }};
            @endforeach
        }
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="theme-{{ $theme->directory() ?? 'modern-news' }}">
    @include('public.partials.header')

    <main>
        @yield('content')
    </main>

    @include('public.partials.footer')

    @stack('scripts')
</body>
</html>
