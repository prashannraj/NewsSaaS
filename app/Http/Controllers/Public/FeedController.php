<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\SeoManager;
use App\Services\ThemeRegistry;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function rss(Request $request, TenantContext $context, SeoManager $seo): Response
    {
        $tenant = $context->get();
        $cacheKey = 'feed:rss:'.$tenant->id.':v'.$tenant->setting('content_version', 1);

        $xml = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($tenant, $seo, $request) {
            return $this->buildRss($tenant, $seo, $request);
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    public function atom(Request $request, TenantContext $context, SeoManager $seo): Response
    {
        $tenant = $context->get();
        $cacheKey = 'feed:atom:'.$tenant->id.':v'.$tenant->setting('content_version', 1);

        $xml = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($tenant, $seo, $request) {
            return $this->buildAtom($tenant, $seo, $request);
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/atom+xml; charset=UTF-8');
    }

    protected function buildRss($tenant, SeoManager $seo, Request $request): string
    {
        $version = $tenant->setting('content_version', 1);

        $articles = Cache::remember(
            'feed_articles:'.$tenant->id.':v'.$version,
            now()->addMinutes(10),
            fn () => Article::published()
                ->where('tenant_id', $tenant->id)
                ->latest('published_at')
                ->limit(20)
                ->get()
        );

        $baseUrl = $seo->canonicalUrl('home', null, $tenant);
        $siteName = $tenant->setting('site_name', 'News Site');
        $description = $tenant->setting('site_description', '');
        $languageCode = $tenant->setting('language_code', 'en');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
        $xml .= '<channel>'."\n";
        $xml .= '<title>'.htmlspecialchars($siteName).'</title>'."\n";
        $xml .= '<link>'.htmlspecialchars($baseUrl).'</link>'."\n";
        $xml .= '<description>'.htmlspecialchars($description).'</description>'."\n";
        $xml .= '<language>'.$languageCode.'</language>'."\n";
        $xml .= '<atom:link href="'.htmlspecialchars($baseUrl.'/feed/rss').'" rel="self" type="application/rss+xml" />'."\n";

        foreach ($articles as $article) {
            $itemUrl = $baseUrl.'/article/'.$article->slug;
            $xml .= '<item>'."\n";
            $xml .= '<title>'.htmlspecialchars($article->title).'</title>'."\n";
            $xml .= '<link>'.htmlspecialchars($itemUrl).'</link>'."\n";
            $xml .= '<guid isPermaLink="false">'.htmlspecialchars($itemUrl).'</guid>'."\n";
            $xml .= '<pubDate>'.gmdate('D, d M Y H:i:s', strtotime($article->published_at)).' GMT</pubDate>'."\n";
            $xml .= '<description>'.htmlspecialchars($article->excerpt ?? '').'</description>'."\n";
            $xml .= '<content:encoded>'.htmlspecialchars($article->body ?? '').'</content:encoded>'."\n";
            $xml .= '<author>'.htmlspecialchars($article->author->email ?? '').'</author>'."\n";
            $xml .= '</item>'."\n";
        }

        $xml .= '</channel>'."\n";
        $xml .= '</rss>';

        return $xml;
    }

    protected function buildAtom($tenant, SeoManager $seo, Request $request): string
    {
        $version = $tenant->setting('content_version', 1);

        $articles = Cache::remember(
            'feed_articles:'.$tenant->id.':v'.$version,
            now()->addMinutes(10),
            fn () => Article::published()
                ->where('tenant_id', $tenant->id)
                ->latest('published_at')
                ->limit(20)
                ->get()
        );

        $baseUrl = $seo->canonicalUrl('home', null, $tenant);
        $siteName = $tenant->setting('site_name', 'News Site');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<feed xmlns="http://www.w3.org/2005/Atom">'."\n";
        $xml .= '<title>'.htmlspecialchars($siteName).'</title>'."\n";
        $xml .= '<link href="'.htmlspecialchars($baseUrl.'/feed/atom').'" rel="self" />'."\n";
        $xml .= '<link href="'.htmlspecialchars($baseUrl).'" />'."\n";
        $xml .= '<id>'.htmlspecialchars($baseUrl.'/feed/atom').'</id>'."\n";
        $xml .= '<updated>'.gmdate('c').'</updated>'."\n";

        foreach ($articles as $article) {
            $itemUrl = $baseUrl.'/article/'.$article->slug;
            $xml .= '<entry>'."\n";
            $xml .= '<title>'.htmlspecialchars($article->title).'</title>'."\n";
            $xml .= '<link href="'.htmlspecialchars($itemUrl).'" />'."\n";
            $xml .= '<id>'.htmlspecialchars($itemUrl).'</id>'."\n";
            $xml .= '<published>'.gmdate('c', strtotime($article->published_at)).'</published>'."\n";
            $xml .= '<updated>'.gmdate('c', strtotime($article->updated_at)).'</updated>'."\n";
            $xml .= '<author><name>'.htmlspecialchars($article->author->name ?? '').'</name></author>'."\n";
            $xml .= '<content type="html">'.htmlspecialchars($article->body ?? '').'</content>'."\n";
            $xml .= '</entry>'."\n";
        }

        $xml .= '</feed>';

        return $xml;
    }
}