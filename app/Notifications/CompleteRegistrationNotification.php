<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompleteRegistrationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $token,
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
        $url = route('registration.complete', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->subject('Complete your registration')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Thanks for signing up. To finish creating your account, please set your password using the link below.')
            ->action('Complete registration', $url)
            ->line('This link will expire in '.$this->expiresInMinutes.' minutes.')
            ->line('If you did not request this account, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}

