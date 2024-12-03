<?php

namespace App\Notifications;

use App\Models\DoctorSubscription;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentFailed extends Notification
{
    public function __construct(
        private DoctorSubscription $subscription
    ) {}

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Failed')
            ->line('Your payment failed. Please update your payment information.')
            ->line('Plan: ' . $this->subscription->plan->name)
            ->action('Update Payment Info', url('/dashboard/payment-method'));
    }
}
