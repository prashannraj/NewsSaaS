<x-layouts::app :title="__('Onboarding')">
    <div class="p-6 space-y-4">
        <h1 class="text-2xl font-bold">{{ $organization->name }}</h1>
        <p>{{ __('Your portal URL:') }} <a href="{{ $domain?->domain ? 'http://'.$domain->domain : '#' }}" class="text-blue-600">{{ $domain?->domain }}</a></p>
        <p>{{ __('Select a plan and configure your custom domain to complete setup.') }}</p>
        <a href="{{ route('dashboard') }}" class="text-blue-600">{{ __('Continue to dashboard') }}</a>
    </div>
</x-layouts::app>
