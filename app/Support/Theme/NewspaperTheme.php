<?php

namespace App\Support\Theme;

use App\Models\TenantTheme;

class NewspaperTheme implements ThemeConfig
{
    public function __construct(protected ?TenantTheme $theme = null)
    {
    }

    public function name(): string
    {
        return $this->theme->name ?? 'Newspaper';
    }

    public function directory(): string
    {
        return 'newspaper';
    }

    public function primaryColor(): string
    {
        return $this->theme->settings['primary_color'] ?? '#2d3748';
    }

    public function secondaryColor(): string
    {
        return $this->theme->settings['secondary_color'] ?? '#718096';
    }

    public function fontSize(): string
    {
        return $this->theme->settings['font_size'] ?? '15px';
    }

    public function lineHeight(): string
    {
        return $this->theme->settings['line_height'] ?? '1.5';
    }

    public function fontFamily(): string
    {
        return $this->theme->settings['font_family'] ?? 'Georgia, "Times New Roman", serif';
    }

    public function layoutHeader(): string
    {
        return $this->theme->settings['header_layout'] ?? 'sticky';
    }

    public function layoutFooter(): string
    {
        return $this->theme->settings['footer_layout'] ?? 'full';
    }

    public function navStyle(): string
    {
        return $this->theme->settings['nav_style'] ?? 'horizontal';
    }

    public function homepageSectionOrder(): array
    {
        $order = $this->theme->settings['homepage_section_order'] ?? [];
        if (empty($order)) {
            $order = ['hero', 'top_stories', 'featured', 'categories', 'most_read'];
        }

        return $order;
    }

    public function featuredStoryLayout(): string
    {
        return $this->theme->settings['featured_story_layout'] ?? 'card';
    }

    public function sidebarEnabled(): bool
    {
        return (bool) ($this->theme->settings['sidebar_enabled'] ?? false);
    }

    public function articleTypography(): string
    {
        return $this->theme->settings['article_typography'] ?? 'newspaper';
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
