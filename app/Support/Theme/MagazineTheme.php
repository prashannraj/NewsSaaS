<?php

namespace App\Support\Theme;

use App\Models\TenantTheme;

class MagazineTheme implements ThemeConfig
{
    public function __construct(protected ?TenantTheme $theme = null)
    {
    }

    public function name(): string
    {
        return $this->theme->name ?? 'Magazine';
    }

    public function directory(): string
    {
        return 'magazine';
    }

    public function primaryColor(): string
    {
        return $this->theme->settings['primary_color'] ?? '#4c1d95';
    }

    public function secondaryColor(): string
    {
        return $this->theme->settings['secondary_color'] ?? '#7c3aed';
    }

    public function fontSize(): string
    {
        return $this->theme->settings['font_size'] ?? '18px';
    }

    public function lineHeight(): string
    {
        return $this->theme->settings['line_height'] ?? '1.7';
    }

    public function fontFamily(): string
    {
        return $this->theme->settings['font_family'] ?? 'system-ui, sans-serif';
    }

    public function layoutHeader(): string
    {
        return $this->theme->settings['header_layout'] ?? 'sticky';
    }

    public function layoutFooter(): string
    {
        return $this->theme->settings['footer_layout'] ?? 'compact';
    }

    public function navStyle(): string
    {
        return $this->theme->settings['nav_style'] ?? 'dropdown';
    }

    public function homepageSectionOrder(): array
    {
        $order = $this->theme->settings['homepage_section_order'] ?? [];
        if (empty($order)) {
            $order = ['hero', 'editorial', 'categories', 'trending', 'newsletter'];
        }

        return $order;
    }

    public function featuredStoryLayout(): string
    {
        return $this->theme->settings['featured_story_layout'] ?? 'masonry';
    }

    public function sidebarEnabled(): bool
    {
        return (bool) ($this->theme->settings['sidebar_enabled'] ?? true);
    }

    public function articleTypography(): string
    {
        return $this->theme->settings['article_typography'] ?? 'magazine';
    }

    public function darkModeEnabled(): bool
    {
        return (bool) ($this->theme->settings['dark_mode_enabled'] ?? true);
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
