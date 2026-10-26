@extends('public.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <article class="prose mx-auto">
        <header class="mb-8">
            <h1 class="text-4xl font-bold mb-4">{{ $article->title }}</h1>
            <div class="flex items-center gap-4 text-sm text-gray-500">
                <span>By {{ $article->author->name ?? 'Staff' }}</span>
                <span>•</span>
                <span>{{ $article->published_at?->format('F j, Y') ?? $article->created_at->format('F j, Y') }}</span>
                <span>•</span>
                <span>{{ $article->category->name ?? 'Uncategorized' }}</span>
            </div>
        </header>

        @if ($article->featuredImage)
            <img src="{{ $article->featuredImage->url }}" alt="{{ $article->title }}" class="w-full h-auto rounded mb-6">
        @endif

        <div class="mt-6">
            {!! $article->body ?? '' !!}
        </div>

        @if ($article->tags && $article->tags->isNotEmpty())
            <div class="mt-8 pt-4 border-t">
                <span class="text-sm font-semibold">Tags:</span>
                @foreach ($article->tags as $articleTag)
                    <a href="{{ route('public.tag.show', $articleTag->slug) }}" class="text-blue-600 text-sm">{{ $articleTag->name }}</a>
                @endforeach
            </div>
        @endif
    </article>
</div>
@endsection
