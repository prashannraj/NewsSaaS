<x-layouts::app :title="__('Invitations')">
    <div class="p-6 space-y-4">
        <form method="POST" action="{{ route('organizations.invitations.store', $organization) }}" class="space-y-2">
            @csrf
            <flux:input name="email" type="email" :label="__('Email')" required />
            <flux:button type="submit" variant="primary">{{ __('Invite') }}</flux:button>
        </form>
        <ul>
            @foreach ($invitations as $invitation)
                <li>{{ $invitation->email }} - {{ $invitation->accepted_at ? __('accepted') : ($invitation->revoked_at ? __('revoked') : __('pending')) }}</li>
            @endforeach
        </ul>
    </div>
</x-layouts::app>
