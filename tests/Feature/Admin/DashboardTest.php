<?php

use App\Enums\Role;
use App\Models\ActivityLog;
use App\Models\Event;
use App\Models\News;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\SubscriptionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows dashboard statistics for admin users', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create(['role' => Role::Member]);

    Event::query()->create([
        'title' => 'Annual Meeting',
        'description' => 'Portal event',
        'location' => 'Jakarta',
        'status' => 'upcoming',
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDays(2),
    ]);

    $plan = SubscriptionPlan::factory()->create();

    Subscription::factory()->create([
        'user_id' => $member->id,
        'subscription_plan_id' => $plan->id,
        'status' => SubscriptionStatus::Active,
    ]);

    News::factory()->published()->count(2)->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Total Users')
        ->assertSee(number_format(User::query()->count()))
        ->assertSee('Total Events')
        ->assertSee(number_format(Event::query()->count()))
        ->assertSee('Active Subscriptions')
        ->assertSee(number_format(Subscription::query()->where('status', SubscriptionStatus::Active->value)->count()))
        ->assertSee('Published News')
        ->assertSee(number_format(News::query()->published()->count()));
});

it('shows recent activity entries on dashboard', function () {
    $admin = User::factory()->admin()->create();
    $member = User::factory()->create();

    ActivityLog::query()->create([
        'user_id' => $member->id,
        'action' => 'Profile updated',
        'description' => 'Member profile details were updated.',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Profile updated')
        ->assertSee($member->email)
        ->assertSee('Member profile details were updated.');
});
