@extends('public.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <h1 class="text-3xl font-bold mb-2">Tag: {{ $tag->name }}</h1>
            <p class="text-gray-600 mb-8">Articles tagged "{{ $tag->name }}"</p>

            @forelse ($articles as $article)
                <article class="border-b border-gray-200 pb-6 mb-6">
                    <h2 class="text-xl font-bold mb-2">
                        <a href="{{ route('public.article.show', $article->slug) }}" class="hover:text-blue-600">
                            {{ $article->title }}
                        </a>
                    </h2>
                    <p class="text-gray-600 mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($article->excerpt ?? $article->body ?? ''), 160) }}</p>
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span>{{ $article->author->name ?? 'Staff' }}</span>
                        <span>{{ $article->published_at?->format('M d, Y') ?? $article->created_at->format('M d, Y') }}</span>
                    </div>
                </article>
            @empty
                <p class="text-gray-500">No articles with this tag.</p>
            @endforelse

            @if ($articles->hasPages())
                <div class="mt-8">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>

        <aside>
            @include('public.partials.sidebar')
        </aside>
    </div>
</div>
@endsection
