<?php

namespace App\Notifications;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrganizationInvitation extends Notification
{
    use Queueable;

    public function __construct(
        protected Organization $organization,
        protected string $token,
        protected User $inviter,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('invitations.accept', ['token' => $this->token]);

        return (new MailMessage)
            ->subject('You are invited to '.$this->organization->name)
            ->line($this->inviter->name.' invited you to join '.$this->organization->name.'.')
            ->action('Accept invitation', $url)
            ->line('This invitation expires in 7 days.');
    }
}
