<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $this->authorize('create', Article::class);

        $data = $request->validated();
        $data['organization_id'] = $request->user()->current_organization_id;
        $data['author_id'] = $request->user()->id;
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        Article::create($data);

        return redirect()->back()->with('status', 'article-created');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->authorize('delete', $article);

        $article->delete();

        return redirect()->back()->with('status', 'article-deleted');
    }
}
