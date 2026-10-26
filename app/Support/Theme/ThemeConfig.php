<?php

namespace App\Support\Theme;

interface ThemeConfig
{
    public function name(): string;

    public function directory(): string;

    public function primaryColor(): string;

    public function secondaryColor(): string;

    public function fontSize(): string;

    public function lineHeight(): string;

    public function fontFamily(): string;

    public function layoutHeader(): string;

    public function layoutFooter(): string;

    public function navStyle(): string;

    public function homepageSectionOrder(): array;

    public function featuredStoryLayout(): string;

    public function sidebarEnabled(): bool;

    public function articleTypography(): string;

    public function darkModeEnabled(): bool;

    public function socialLinks(): array;

    public function customCss(): string;

    public function cssVariables(): array;
}
