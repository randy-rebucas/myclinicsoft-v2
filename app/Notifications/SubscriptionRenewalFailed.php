<?php

namespace App\Notifications;

use App\Models\DoctorSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionRenewalFailed extends Notification
{
    use Queueable;

    public function __construct(
        private DoctorSubscription $subscription
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->error()
            ->subject('Subscription Renewal Failed')
            ->line('We were unable to renew your subscription automatically.')
            ->action('Update Payment Method', url('/billing'));
    }
}
