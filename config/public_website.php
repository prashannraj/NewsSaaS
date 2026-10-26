<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public Website Rendering
    |--------------------------------------------------------------------------
    |
    | Configuration for the multi-tenant public website rendering engine.
    | These values control caching, SEO behaviour, and content limits.
    |
    */

    // Number of items per page in list views
    'items_per_page' => (int) env('NEWS_PUBLIC_ITEMS_PER_PAGE', 10),

    // Cache duration (in seconds) for rendered pages
    'cache_ttl_seconds' => (int) env('NEWS_PUBLIC_CACHE_TTL', 300),

    // Default theme if a tenant has no explicit theme
    'default_theme' => env('NEWS_DEFAULT_THEME', 'modern-news'),

    // Whether to allow indexing by search engines
    'allow_indexing' => (bool) env('NEWS_ALLOW_INDEXING', true),

    // Available themes
    'available_themes' => [
        'modern-news'   => 'Modern News',
        'newspaper'     => 'Newspaper',
        'magazine'      => 'Magazine',
        'minimal'       => 'Minimal',
        'business-news' => 'Business News',
    ],

    // Content security policy header value (set to null to disable)
    'content_security_policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https://fonts.gstatic.com; connect-src 'self';",

];
