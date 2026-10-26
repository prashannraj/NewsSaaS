<?php

namespace App\Support\Theme;

use App\Models\TenantTheme;

class ThemeRegistry
{
    private array $themes = [
        'modern-news' => ModernNewsTheme::class,
        'newspaper' => NewspaperTheme::class,
        'magazine' => MagazineTheme::class,
        'minimal' => MinimalTheme::class,
        'business-news' => BusinessNewsTheme::class,
    ];

    public function available(): array
    {
        return array_keys($this->themes);
    }

    public function make(string $directory, ?TenantTheme $theme = null): ThemeConfig
    {
        $class = $this->themes[$directory] ?? ModernNewsTheme::class;

        return new $class($theme);
    }

    public function resolve(?TenantTheme $theme = null, ?string $directory = null): ThemeConfig
    {
        if ($theme !== null && isset($this->themes[$theme->directory ?? ''])) {
            return $this->make($theme->directory, $theme);
        }

        if ($directory !== null && isset($this->themes[$directory])) {
            return $this->make($directory);
        }

        return $this->make('modern-news');
    }
}