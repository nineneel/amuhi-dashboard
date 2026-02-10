<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $code,
        public string $context,
        public int $expiresInMinutes
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $contextLabel = match ($this->context) {
            'enable' => 'Confirm two-factor authentication',
            'login' => 'Complete your sign in',
            default => 'Two-factor authentication code',
        };

        return (new MailMessage)
            ->subject($contextLabel)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your verification code is:')
            ->line($this->code)
            ->line('This code will expire in '.$this->expiresInMinutes.' minutes.')
            ->line('If you did not request this code, please secure your account.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'context' => $this->context,
        ];
    }
}
