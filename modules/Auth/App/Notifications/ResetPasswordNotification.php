<?php

namespace Modules\Auth\App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(
        public readonly string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(config('app.frontend_url', config('app.url'))
            . '/reset-password?token=' . $this->token
            . '&email=' . urlencode($notifiable->getEmailForPasswordReset()));

        return (new MailMessage)
            ->subject(__('auth::auth.reset_password_subject'))
            ->line(__('auth::auth.reset_password_line'))
            ->action(__('auth::auth.reset_password_action'), $url)
            ->line(__('auth::auth.reset_password_expiry', [
                'count' => config('auth.passwords.users.expire'),
            ]))
            ->line(__('auth::auth.reset_password_no_action'));
    }
}
