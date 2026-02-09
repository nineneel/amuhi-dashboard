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
        SubscriptionPlan::create([
            'name' => 'Annual Membership',
            'description' => 'Full access to all AMUHI programs and events for one year',
            'price' => 500000,
            'duration_days' => 365,
            'features' => [
                'Access to all 6 AMUHI programs',
                'Event registration',
                'Digital certificate',
                'Network access',
                'Priority support',
            ],
            'is_active' => true,
        ]);

        SubscriptionPlan::create([
            'name' => 'Monthly Membership',
            'description' => 'Full access to all AMUHI programs and events for one month',
            'price' => 50000,
            'duration_days' => 30,
            'features' => [
                'Access to all 6 AMUHI programs',
                'Event registration',
                'Network access',
            ],
            'is_active' => true,
        ]);
    }
}
