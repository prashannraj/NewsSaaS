@extends('public.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-2xl">
    <h1 class="text-4xl font-bold mb-6">Contact Us</h1>
    <p class="text-gray-600 mb-8">Have questions or feedback? Send us a message.</p>

    @if (session('status') === 'contact-sent')
        <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">
            Your message has been sent. We'll get back to you soon.
        </div>
    @endif

    <form method="POST" action="{{ route('public.contact.submit') }}" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-2">Name</label>
            <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Email</label>
            <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Subject</label>
            <input type="text" name="subject" required class="w-full px-4 py-2 border border-gray-300 rounded">
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Message</label>
            <textarea name="message" rows="6" required class="w-full px-4 py-2 border border-gray-300 rounded"></textarea>
        </div>
        <div>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded">Send Message</button>
        </div>
    </form>
</div>
@endsection
