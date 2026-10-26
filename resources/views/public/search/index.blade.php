@extends('public.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">Search</h1>

    <form method="GET" action="{{ route('public.search') }}" class="mb-8">
        <div class="flex gap-2">
            <input
                type="text"
                name="q"
                value="{{ $query ?? '' }}"
                placeholder="Search for articles..."
                class="flex-1 px-4 py-2 border border-gray-300 rounded"
            >
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Search</button>
        </div>
    </form>

    @if ($query)
        @forelse ($articles ?? collect() as $article)
            <div class="border-b border-gray-200 pb-4 mb-4">
                <h2 class="text-xl font-bold mb-2">
                    <a href="{{ route('public.article.show', $article->slug) }}" class="hover:text-blue-600">
                        {{ $article->title }}
                    </a>
                </h2>
                <p class="text-gray-600 mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($article->excerpt ?? $article->body ?? ''), 160) }}</p>
                <div class="text-sm text-gray-500">
                    {{ $article->published_at?->format('M d, Y') ?? $article->created_at->format('M d, Y') }}
                </div>
            </div>
        @empty
            <p class="text-gray-500">No results found for "{{ $query }}".</p>
        @endforelse

        @if (isset($articles) && $articles->hasPages())
            <div class="mt-6">
                {{ $articles->links() }}
            </div>
        @endif
    @else
        <p class="text-gray-500">Enter a search term to find articles.</p>
    @endif
</div>
@endsection
