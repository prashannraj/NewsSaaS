@extends('public.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <h1 class="text-4xl font-bold mb-6">Privacy Policy</h1>
    <div class="prose mx-auto">
        @if (isset($content) && $content)
            {!! $content->content ?? '' !!}
        @else
            <p>Your privacy is important to us. This policy outlines how we collect, use, and protect your information.</p>
        @endif
    </div>
</div>
@endsection
