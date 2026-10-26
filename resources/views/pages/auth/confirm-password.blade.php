<x-layouts::auth :title="__('Confirm password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Confirm password')" :description="__('This is a secure area. Please confirm your password before continuing.')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf
            <flux:input name="password" :label="__('Password')" type="password" required autocomplete="current-password" viewable />
            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Confirm') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
