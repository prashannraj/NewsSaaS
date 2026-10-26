@extends('public.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <h1 class="text-4xl font-bold mb-6">Terms of Service</h1>
    <div class="prose mx-auto">
        @if (isset($content) && $content)
            {!! $content->content ?? '' !!}
        @else
            <p>By using this service, you agree to these terms of service. Please review them carefully.</p>
        @endif
    </div>
</div>
@endsection
