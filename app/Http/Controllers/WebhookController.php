<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use App\Services\SubscriptionService;
use App\Models\DoctorSubscription;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleStripeWebhook(Request $request, SubscriptionService $subscriptionService)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $subscription = DoctorSubscription::find($paymentIntent->metadata->subscription_id);
                    $subscriptionService->handlePaymentSuccess($subscription);
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $subscription = DoctorSubscription::find($paymentIntent->metadata->subscription_id);
                    $subscriptionService->handlePaymentFailure($subscription);
                    break;

                default:
                    Log::info('Unhandled event type: ' . $event->type);
            }

            return response()->json(['status' => 'success']);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }
}
