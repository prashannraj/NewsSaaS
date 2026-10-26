<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\Tag;
use App\Services\SeoManager;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(TenantContext $context, SeoManager $seo): Response
    {
        $tenant = $context->get();
        $cacheKey = 'sitemap:'.$tenant->id.':'.$tenant->setting('content_version', 1);

        $xml = Cache::remember($cacheKey, now()->addHours(1), function () use ($tenant, $seo) {
            $urls = [];

            $urls[] = $this->urlEntry($seo->canonicalUrl('home', null, $tenant), now(), '1.0', 'daily');

            foreach (Category::where('tenant_id', $tenant->id)->where('is_active', true)->get() as $category) {
                $urls[] = $this->urlEntry($seo->canonicalUrl('category', $category, $tenant), $category->updated_at ?? now(), '0.8', 'daily');
            }

            foreach (Tag::where('tenant_id', $tenant->id)->get() as $tag) {
                $urls[] = $this->urlEntry($seo->canonicalUrl('tag', $tag, $tenant), now(), '0.6', 'weekly');
            }

            foreach (Page::where('tenant_id', $tenant->id)->where('status', 'published')->get() as $page) {
                $urls[] = $this->urlEntry($seo->canonicalUrl('page', $page, $tenant), $page->updated_at ?? now(), '0.7', 'monthly');
            }

            foreach (Article::published()->where('tenant_id', $tenant->id)->latest('published_at')->limit(1000)->get() as $article) {
                $urls[] = $this->urlEntry($seo->canonicalUrl('article', $article, $tenant), $article->published_at, '0.9', 'daily');
            }

            $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

            foreach ($urls as $entry) {
                $xml .= $entry;
            }

            $xml .= '</urlset>';

            return $xml;
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    protected function urlEntry(string $loc, $lastMod, string $priority, string $changefreq): string
    {
        $lastModStr = $lastMod instanceof \Illuminate\Support\Carbon ? $lastMod->format('Y-m-d') : date('Y-m-d', strtotime($lastMod));

        return "<url>\n<loc>".htmlspecialchars($loc)."</loc>\n<lastmod>{$lastModStr}</lastmod>\n<priority>{$priority}</priority>\n<changefreq>{$changefreq}</changefreq>\n</url>\n";
    }
}