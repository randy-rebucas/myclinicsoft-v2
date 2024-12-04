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
            'plan_amount' => 0,
            'billing_cycle' => 'monthly',
            'features' => [
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Limit Access to App features']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can only add 10 Patients']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can only add 1 Receptionist']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can only add 1 Med Representative']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can only add 10 Medications']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can only print 10 Prescription Forms']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can only add 10 Patient Records']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Advertisements on Queue Not Available']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'No Updates']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'No Support']
                ],
            ]
        ]);

        SubscriptionPlan::create([
            'name' => 'Basic',
            'description' => 'Basic features for doctors',
            'plan_amount' => 1250,
            'billing_cycle' => 'monthly',
            'features' => [
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Full Access to App features.']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can add unlimited Patients.']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can add unlimited Receptionists']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can add unlimited Med Representatives']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can add unlimited Medications']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can print unlimited Prescription Forms']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can add unlimited Patient Records']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Free 5 second Advertisements on Queue']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Paid Updates']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => '24/7 Paid Support']
                ]
            ]
        ]);

        SubscriptionPlan::create([
            'name' => 'Premium',
            'description' => 'Premium features for doctors',
            'plan_amount' => 23250,
            'billing_cycle' => 'yearly',
            'features' => [
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Full Access to App features.']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can add unlimited Patients.']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can add unlimited Receptionists']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can add unlimited Med Representatives']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can add unlimited Medications']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can print unlimited Prescription Forms']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Can add unlimited Patient Records']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Free 5 second Advertisements on Queue']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Free Updates']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => '24/7 Support']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Priority Support']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Priority Updates']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Priority Feature Updates']
                ],
                [
                    'type' => 'subscription-feature',
                    'fields' => ['feature' => 'Priority Feature Additions']
                ]
            ]
        ]);

    }
}
