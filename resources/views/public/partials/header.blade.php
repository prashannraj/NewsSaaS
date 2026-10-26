<header class="site-header" style="background-color: {{ $theme->primaryColor() ?? '#1a365d' }};">
    <div class="container">
        <div class="flex items-center justify-between py-4">
            <a href="{{ route('public.home') }}" class="text-white text-2xl font-bold">
                {{ $tenant->settings()->where('key', 'site_name')->value('value') ?? 'News Site' }}
            </a>

            <nav class="flex items-center gap-4">
                @foreach (($tenantNav ?? collect()) as $item)
                    <a href="{{ $item['url'] }}" class="text-white hover:text-gray-200 px-3 py-2 rounded text-sm">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
</header>
