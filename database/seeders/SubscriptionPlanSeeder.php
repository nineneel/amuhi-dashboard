<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionPlan::query()->updateOrCreate(
            ['name' => 'Annual Membership'],
            [
                'description' => 'Annual access to all AMUHI programs and events',
                'price' => 3000000,
                'duration_days' => 365,
                'features' => [
                    'Access to all 6 AMUHI programs',
                    'Event registration',
                    'Digital certificate',
                    'Network access',
                    'Priority support',
                ],
                'is_active' => true,
            ]
        );

        SubscriptionPlan::query()->updateOrCreate(
            ['name' => 'Register as Member'],
            [
                'description' => 'One-time registration fee to become an AMUHI member',
                'price' => 2000000,
                'duration_days' => 0,
                'features' => [
                    'Membership registration processing',
                    'Member onboarding',
                ],
                'is_active' => true,
            ]
        );

        SubscriptionPlan::query()
            ->where('name', 'Monthly Membership')
            ->update(['is_active' => false]);
    }
}
