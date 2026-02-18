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
        ->assertSee('class="h-full dark"', false)
        ->assertSee('class="dark bg-gray-900"', false)
        ->assertSee('data-theme-scope="dark"', false)
        ->assertSee(__('ui.payments.subscription_active'))
        ->assertDontSee(__('ui.payments.pay_now_demo'));
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

it('treats an ended active subscription as expired', function () {
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
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDay(),
        ]);

    $this->actingAs($user)
        ->get(route('payments.show'))
        ->assertSuccessful()
        ->assertSee(__('ui.subscriptions.status.expired'))
        ->assertSee(__('ui.payments.pay_now_demo'));
});

it('does not charge the registration fee again for existing members', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $registerPlan = SubscriptionPlan::factory()->create([
        'name' => 'Register as Member',
        'price' => 2000000,
        'duration_days' => 0,
        'is_active' => true,
    ]);

    $annualPlan = SubscriptionPlan::factory()->create([
        'name' => 'Annual Membership',
        'price' => 3000000,
        'duration_days' => 365,
        'is_active' => true,
    ]);

    Subscription::factory()
        ->for($user)
        ->for($annualPlan)
        ->create([
            'status' => SubscriptionStatus::Expired,
            'starts_at' => now()->subYear(),
            'ends_at' => now()->subDay(),
        ]);

    $this->actingAs($user)
        ->get(route('payments.show'))
        ->assertSuccessful()
        ->assertDontSee($registerPlan->name)
        ->assertSee('Rp 3.000.000');
});
