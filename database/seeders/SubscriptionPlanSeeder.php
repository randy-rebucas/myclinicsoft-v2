<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        SubscriptionPlan::create([
            'name' => 'Free',
            'description' => 'Free features for doctors',
            'price' => 0,
            'billing_period' => 'monthly',
            'features' => ['feature1']
        ]);

        SubscriptionPlan::create([
            'name' => 'Basic',
            'description' => 'Basic features for doctors',
            'price' => 29.99,
            'billing_period' => 'monthly',
            'features' => ['feature1', 'feature2']
        ]);

        SubscriptionPlan::create([
            'name' => 'Premium',
            'description' => 'Premium features for doctors',
            'price' => 299.99,
            'billing_period' => 'yearly',
            'features' => ['feature1', 'feature2', 'feature3', 'feature4']
        ]);

    }
}
