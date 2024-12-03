<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\DoctorSubscription;
use Exception;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(DoctorSubscription $subscription): PaymentIntent
    {
        try {
            return PaymentIntent::create([
                'amount' => $subscription->plan->price * 100, // Stripe uses cents
                'currency' => 'usd',
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'doctor_id' => $subscription->doctor_id
                ]
            ]);
        } catch (Exception $e) {
            report($e);
            throw new Exception('Payment processing failed: ' . $e->getMessage());
        }
    }
}
