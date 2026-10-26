<?php

namespace App\Support\Theme;

use App\Models\TenantTheme;

class MinimalTheme implements ThemeConfig
{
    public function __construct(protected ?TenantTheme $theme = null)
    {
    }

    public function name(): string
    {
        return $this->theme->name ?? 'Minimal';
    }

    public function directory(): string
    {
        return 'minimal';
    }

    public function primaryColor(): string
    {
        return $this->theme->settings['primary_color'] ?? '#111827';
    }

    public function secondaryColor(): string
    {
        return $this->theme->settings['secondary_color'] ?? '#374151';
    }

    public function fontSize(): string
    {
        return $this->theme->settings['font_size'] ?? '18px';
    }

    public function lineHeight(): string
    {
        return $this->theme->settings['line_height'] ?? '1.8';
    }

    public function fontFamily(): string
    {
        return $this->theme->settings['font_family'] ?? 'Georgia, serif';
    }

    public function layoutHeader(): string
    {
        return $this->theme->settings['header_layout'] ?? 'static';
    }

    public function layoutFooter(): string
    {
        return $this->theme->settings['footer_layout'] ?? 'minimal';
    }

    public function navStyle(): string
    {
        return $this->theme->settings['nav_style'] ?? 'horizontal';
    }

    public function homepageSectionOrder(): array
    {
        $order = $this->theme->settings['homepage_section_order'] ?? [];
        if (empty($order)) {
            $order = ['hero', 'latest', 'categories'];
        }

        return $order;
    }

    public function featuredStoryLayout(): string
    {
        return $this->theme->settings['featured_story_layout'] ?? 'wide';
    }

    public function sidebarEnabled(): bool
    {
        return (bool) ($this->theme->settings['sidebar_enabled'] ?? false);
    }

    public function articleTypography(): string
    {
        return $this->theme->settings['article_typography'] ?? 'reader';
    }

    public function darkModeEnabled(): bool
    {
        return (bool) ($this->theme->settings['dark_mode_enabled'] ?? false);
    }

    public function socialLinks(): array
    {
        $links = $this->theme->settings['social_links'] ?? [];
        if (! is_array($links)) {
            $links = [];
        }

        return $links;
    }

    public function customCss(): string
    {
        return (string) ($this->theme->settings['custom_css'] ?? '');
    }

    public function cssVariables(): array
    {
        $vars = $this->theme->settings['css_variables'] ?? [];
        if (! is_array($vars)) {
            $vars = [];
        }

        return $vars;
    }
}
