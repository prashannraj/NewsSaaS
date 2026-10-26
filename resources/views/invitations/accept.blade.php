<x-layouts::auth :title="__('Accept invitation')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Accept invitation')" :description="__('You have been invited to join an organization.')" />
        @auth
            <form method="POST" action="{{ route('invitations.accept.store', $token) }}">
                @csrf
                <flux:button type="submit" variant="primary" class="w-full">{{ __('Accept invitation') }}</flux:button>
            </form>
        @else
            <flux:link :href="route('login')">{{ __('Log in to accept') }}</flux:link>
        @endauth
    </div>
</x-layouts::auth>
