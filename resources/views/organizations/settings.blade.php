<x-layouts::app :title="__('Organization settings')">
    <div class="p-6">
        <form method="POST" action="{{ route('organizations.settings.update', $organization) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <flux:input name="name" :label="__('Name')" :value="old('name', $organization->name)" />
            <flux:input name="contact_email" :label="__('Contact email')" :value="old('contact_email', $organization->contact_email)" />
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </form>
    </div>
</x-layouts::app>
