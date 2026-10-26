<x-layouts::auth :title="__('Verify email')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Verify your email')" :description="__('Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.')" />

        @if (session('status') === 'verification-link-sent')
            <x-auth-session-status class="text-center" :status="__('A new verification link has been sent to your email address.')" />
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="flex flex-col gap-6">
            @csrf
            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Resend verification email') }}
            </flux:button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <flux:button variant="ghost" type="submit" class="w-full">
                {{ __('Log out') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
