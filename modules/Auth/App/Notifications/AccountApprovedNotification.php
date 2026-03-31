<?php

namespace Modules\Auth\App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountApprovedNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('auth::auth.account_approved_subject'))
            ->line(__('auth::auth.account_approved_line'))
            ->action(__('auth::auth.account_approved_action'), url('/login'));
    }
}
