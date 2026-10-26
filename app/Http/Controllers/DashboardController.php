<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $organization = $user?->currentOrganization;
        $articles = $organization ? Article::withoutGlobalScopes()->where('organization_id', $organization->id)->latest()->take(10)->get() : collect();

        return view('dashboard', compact('organization', 'articles'));
    }
}
