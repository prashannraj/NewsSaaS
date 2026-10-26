@extends('public.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            @forelse ($articles as $article)
                <article class="border-b border-gray-200 pb-6">
                    <h2 class="text-2xl font-bold mb-2">
                        <a href="{{ route('public.article.show', $article->slug) }}" class="hover:text-blue-600">
                            {{ $article->title }}
                        </a>
                    </h2>
                    <p class="text-gray-600 mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($article->excerpt ?? $article->body ?? ''), 160) }}</p>
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span>{{ $article->author->name ?? 'Staff' }}</span>
                        <span>{{ $article->published_at?->format('M d, Y') ?? $article->created_at->format('M d, Y') }}</span>
                        <span>{{ $article->category->name ?? 'Uncategorized' }}</span>
                    </div>
                </article>
            @empty
                <p class="text-gray-500">No featured articles found.</p>
            @endforelse
        </div>

        <aside class="space-y-6">
            <div class="bg-gray-50 p-4 rounded">
                <h3 class="font-bold mb-3">Categories</h3>
                <ul class="space-y-2">
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('public.category.show', $category->slug) }}" class="text-blue-600 hover:underline">
                                {{ $category->name }} ({{ $category->articles_count ?? 0 }})
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-gray-50 p-4 rounded">
                <h3 class="font-bold mb-3">Latest News</h3>
                <ul class="space-y-3">
                    @foreach ($latest as $item)
                        <li>
                            <a href="{{ route('public.article.show', $item->slug) }}" class="text-sm hover:text-blue-600">
                                {{ \Illuminate\Support\Str::limit($item->title, 60) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>
</div>
@endsection
