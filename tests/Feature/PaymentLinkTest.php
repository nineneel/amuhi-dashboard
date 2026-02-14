<?php

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\SubscriptionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows a safe subscription message when the member is already paid', function () {
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
        ->get(route('payments.show'))
        ->assertSuccessful()
        ->assertSee('Your subscription is active.')
        ->assertDontSee('Pay Now (Demo)');
});

it('prevents paying again when the member is already paid', function () {
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
        ->post(route('payments.simulate'), [
            'plan_id' => $plan->id,
        ])
        ->assertRedirect(route('payments.show'))
        ->assertSessionHas('warning');
});

