<?php

namespace App\Notifications;

use App\Models\DoctorSubscription;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentSuccessful extends Notification
{
    public function __construct(
        private DoctorSubscription $subscription
    ) {}

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Successful')
            ->line('Your payment was successful.')
            ->line('Plan: ' . $this->subscription->plan->name)
            ->line('Valid until: ' . $this->subscription->ends_at->format('Y-m-d'))
            ->action('View Subscription', url('/dashboard/subscription'));
    }
}
