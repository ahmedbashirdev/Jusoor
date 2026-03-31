<?php

namespace Modules\Auth\App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject(__('auth::auth.verify_email_subject'))
            ->line(__('auth::auth.verify_email_line'))
            ->action(__('auth::auth.verify_email_action'), $url)
            ->line(__('auth::auth.verify_email_no_action'));
    }
}
