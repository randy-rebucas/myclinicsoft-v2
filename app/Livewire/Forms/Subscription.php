<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Facades\Auth;
use App\Services\SubscriptionService;

class Subscription extends Form
{
    #[Validate('required|email')]
    public $email = '';

    #[Validate('required')]
    public $name = '';

    #[Validate('required')]
    public $paymentMethod;

    #[Validate('required')]
    public $plan;

    public function createSubscription()
    {
        $this->validate();

        try {
            $doctor = Auth::user()->doctor;

            $subscriptionService = new SubscriptionService();
            $subscriptionService->subscribe($doctor, $this->plan);

            // $subscriptionService->checkSubscriptionStatus();
            // $subscriptionService->cancelSubscription($doctor->activeSubscription);

            return [
                'status' => 'success',
                'message' => 'Subscription created successfully!'
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
