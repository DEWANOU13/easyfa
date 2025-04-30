<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IdentityVerificationNotification extends Notification
{
    use Queueable;

    protected $token, $subjet, $view;

    public function __construct($token,$subjet, $view)
    {
        $this->token = $token;
        $this->subjet = $subjet;
        $this->view = $view;
    }

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
        // $url = url('/verify-identity-add?token=' . $this->token);
        return (new MailMessage)
            ->subject($this->subjet)
            ->view($this->view, ['code' => $this->token, 'user' => $notifiable]);

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
