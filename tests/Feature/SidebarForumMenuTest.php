<?php

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\SubscriptionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows forum in the sidebar for subscribed members', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $plan = SubscriptionPlan::factory()->create([
        'duration_days' => 365,
        'is_active' => true,
    ]);

    Subscription::factory()
        ->for($user)
        ->for($plan)
        ->create([
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(30),
        ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee(__('ui.nav.forum'))
        ->assertSee('href="/forum"', false);
});

