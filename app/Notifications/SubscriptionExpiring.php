<?php

namespace App\Notifications;

use App\Models\DoctorSubscription;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExpiring extends Notification
{
    public function __construct(
        private DoctorSubscription $subscription
    ) {}

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Subscription Expiring Soon')
            ->line('Your subscription is expiring soon.')
            ->line('Plan: ' . $this->subscription->plan->name)
            ->line('Expires on: ' . $this->subscription->ends_at->format('Y-m-d'))
            ->action('Renew Subscription', url('/dashboard/subscription'));
    }
}
