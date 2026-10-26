<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\SeoManager;
use App\Support\TenantContext;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(TenantContext $context, SeoManager $seo): Response
    {
        $tenant = $context->get();
        $content = $seo->robotsTxt($tenant);

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}