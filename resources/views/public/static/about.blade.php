@extends('public.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <h1 class="text-4xl font-bold mb-6">About Us</h1>
    <div class="prose mx-auto">
        @if (isset($content) && $content)
            {!! $content->content ?? '' !!}
        @else
            <p>We are a news organization committed to delivering accurate, timely, and impactful journalism to our community.</p>
        @endif
    </div>
</div>
@endsection
