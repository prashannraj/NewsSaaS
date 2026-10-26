<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendContactFormNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $tenantId,
        public array $data,
    ) {
    }

    public function handle(): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (! $tenant) {
            return;
        }

        // Look up the admin/contact email from tenant settings
        $contactEmail = \App\Models\TenantSetting::where('tenant_id', $tenant->id)
            ->where('key', 'contact_email')
            ->value('value');

        if (! $contactEmail) {
            return;
        }

        $siteName = $tenant->settings()->where('key', 'site_name')->value('value') ?: 'News Site';

        // Send notification email (using log driver by default)
        \Illuminate\Support\Facades\Log::info('Contact form submission for tenant '.$tenant->id, [
            'to' => $contactEmail,
            'from' => $this->data['email'],
            'subject' => $this->data['subject'],
            'name' => $this->data['name'],
            'message' => $this->data['message'],
        ]);
    }
}
