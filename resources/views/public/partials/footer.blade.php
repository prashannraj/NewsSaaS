<footer class="site-footer bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ $tenant->settings()->where('key', 'site_name')->value('value') ?? 'News Site' }}</h3>
                <p class="text-gray-400 text-sm">{{ $tenant->settings()->where('key', 'site_description')->value('value') ?? '' }}</p>
            </div>

            <div>
                <h4 class="font-semibold mb-3">Categories</h4>
                <ul class="space-y-2 text-sm">
                    @foreach ($footerCategories ?? collect() as $category)
                        <li><a href="{{ route('public.category.show', $category->slug) }}" class="text-gray-300 hover:text-white">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-3">Social</h4>
                <ul class="space-y-2 text-sm">
                    @foreach ($theme->socialLinks() ?? [] as $link)
                        <li><a href="{{ $link['url'] ?? '#' }}" class="text-gray-300 hover:text-white">{{ $link['label'] ?? 'Social' }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-3">Contact</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('public.about') }}" class="text-gray-300 hover:text-white">About</a></li>
                    <li><a href="{{ route('public.contact') }}" class="text-gray-300 hover:text-white">Contact</a></li>
                    <li><a href="{{ route('public.privacy') }}" class="text-gray-300 hover:text-white">Privacy</a></li>
                    <li><a href="{{ route('public.terms') }}" class="text-gray-300 hover:text-white">Terms</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-4 text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ $tenant->settings()->where('key', 'site_name')->value('value') ?? 'News Site' }}. All rights reserved.
        </div>
    </div>
</footer>
