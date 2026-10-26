<?php

use App\Http\Controllers\Public\ArticleController;
use App\Http\Controllers\Public\CategoryController;
use App\Http\Controllers\Public\FeedController;
use App\Http\Controllers\Public\HomepageController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\RobotsController;
use App\Http\Controllers\Public\SearchController;
use App\Http\Controllers\Public\SitemapController;
use App\Http\Controllers\Public\StaticPageController;
use App\Http\Controllers\Public\TagController;
use Illuminate\Support\Facades\Route;

/**
 * Public website routes – served on the tenant domain.
 * All routes are wrapped in the 'tenant' middleware (ResolveTenant)
 * so $tenant and $theme are available in every controller.
 */
Route::middleware(['tenant', 'web'])->group(function () {
    // Homepage
    Route::get('/', HomepageController::class)->name('public.home');

    // Search
    Route::get('search', SearchController::class)->name('public.search');

    // RSS / Atom feeds
    Route::get('feed/rss', [FeedController::class, 'rss'])->name('public.feed.rss');
    Route::get('feed/atom', [FeedController::class, 'atom'])->name('public.feed.atom');

    // Robots.txt & Sitemap
    Route::get('robots.txt', RobotsController::class)->name('public.robots');
    Route::get('sitemap.xml', SitemapController::class)->name('public.sitemap');

    // Static pages
    Route::get('about', [StaticPageController::class, 'show'])->name('public.about');
    Route::get('contact', [StaticPageController::class, 'contact'])->name('public.contact');
    Route::post('contact', [StaticPageController::class, 'submitContact'])->name('public.contact.submit');
    Route::get('privacy', [StaticPageController::class, 'show'])->name('public.privacy');
    Route::get('terms', [StaticPageController::class, 'show'])->name('public.terms');

    // Dynamic pages
    Route::get('page/{slug}', [PageController::class, 'show'])->name('public.page.show');

    // Category archive
    Route::get('category/{slug}', [CategoryController::class, 'show'])->name('public.category.show');

    // Tag archive
    Route::get('tag/{slug}', [TagController::class, 'show'])->name('public.tag.show');

    // Article detail
    Route::get('article/{slug}', [ArticleController::class, 'show'])->name('public.article.show');
});