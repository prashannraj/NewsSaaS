@extends('public.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <h1 class="text-4xl font-bold mb-6">{{ $page->title }}</h1>
    <div class="prose mx-auto">
        {!! $page->content ?? '' !!}
    </div>
</div>
@endsection
