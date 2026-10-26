<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SeoManager
{
    public function title(string $type, mixed $model = null, ?Tenant $tenant = null): string
    {
        $siteName = $this->siteName($tenant);
        $separator = ' | ';

        $title = match ($type) {
            'home' => $siteName.' - '.$this->siteDescription($tenant),
            'article' => $model instanceof Model ? $model->title.' - '.$siteName : $siteName,
            'category' => $model ? $model->name.' - '.$siteName : $siteName,
            'tag' => $model ? $model->name.' - '.$siteName : $siteName,
            'page' => $model ? $model->title.' - '.$siteName : $siteName,
            'static' => match ($model) {
                'about' => 'About Us - '.$siteName,
                'contact' => 'Contact Us - '.$siteName,
                'privacy' => 'Privacy Policy - '.$siteName,
                'terms' => 'Terms of Service - '.$siteName,
                default => $siteName,
            },
            'search' => 'Search - '.$siteName,
            default => $siteName,
        };

        return $title.$separator.$siteName;
    }

    public function description(string $type, mixed $model = null, ?Tenant $tenant = null): string
    {
        $default = $this->siteDescription($tenant);

        if ($model instanceof Model) {
            if ($model->seo_description) {
                return $model->seo_description;
            }

            if ($model->excerpt) {
                return Str::limit(strip_tags($model->excerpt), 160);
            }

            if ($model->body) {
                return Str::limit(strip_tags($model->body), 160);
            }
        }

        return $default;
    }

    public function canonicalUrl(string $type, mixed $model = null, ?Tenant $tenant = null): ?string
    {
        if ($model instanceof Model && isset($model->canonical_url) && $model->canonical_url) {
            return $model->canonical_url;
        }

        $url = $this->tenantUrl($tenant);

        $path = match ($type) {
            'home' => '/',
            'article' => $model ? '/article/'.$model->slug : null,
            'category' => $model ? '/category/'.$model->slug : null,
            'tag' => $model ? '/tag/'.$model->slug : null,
            'page' => $model ? '/page/'.$model->slug : null,
            default => null,
        };

        if ($path === null) {
            return null;
        }

        return $url.$path;
    }

    public function jsonLd(string $type, mixed $model = null, ?Tenant $tenant = null): array
    {
        $siteName = $this->siteName($tenant);

        if ($type === 'article' && $model instanceof Model) {
            $authorName = $model->author?->name ?? $siteName;
            $publishedAt = $model->published_at ?? $model->created_at;
            $imageUrl = $model->featuredImage?->url ?? null;

            $data = [
                '@context' => 'https://schema.org',
                '@type' => 'NewsArticle',
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $this->canonicalUrl('article', $model, $tenant),
                ],
                'headline' => $model->title,
                'image' => [$imageUrl],
                'datePublished' => $publishedAt?->toIso8601String(),
                'dateModified' => $model->updated_at?->toIso8601String(),
                'author' => [
                    '@type' => 'Person',
                    'name' => $authorName,
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $siteName,
                ],
            ];

            return array_filter($data, fn ($value) => $value !== null && $value !== []);
        }

        if ($type === 'organization' && $tenant) {
            return [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => $this->tenantUrl($tenant),
                'logo' => $this->tenantUrl($tenant).'/logo.png',
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $siteName,
            'url' => $this->tenantUrl($tenant),
        ];
    }

    public function robotsTxt(?Tenant $tenant = null): string
    {
        $baseUrl = $this->tenantUrl($tenant);
        $allowAll = filter_var(config('app.public_allow_indexing', true), FILTER_VALIDATE_BOOLEAN);

        $rules = $allowAll
            ? "User-agent: *\nAllow: /\nSitemap: {$baseUrl}/sitemap.xml\n"
            : "User-agent: *\nDisallow: /\n";

        return $rules;
    }

    protected function siteName(?Tenant $tenant = null): string
    {
        return $tenant?->setting('site_name', config('app.name', 'News Site'));
    }

    protected function siteDescription(?Tenant $tenant = null): string
    {
        return $tenant?->setting('site_description', 'News and information');
    }

    protected function setting(?Tenant $tenant, string $key, mixed $default): mixed
    {
        return $tenant?->setting($key, $default);
    }

    protected function tenantUrl(?Tenant $tenant = null): string
    {
        if (! $tenant) {
            return config('app.url', 'http://localhost');
        }

        $domain = $tenant->primaryDomain?->domain ?? ($tenant->subdomain.'.'.config('tenancy.subdomain_suffix', 'localhost'));

        return 'http://'.$domain;
    }
}