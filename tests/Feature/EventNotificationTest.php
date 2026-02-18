<?php

use App\EventStatus;
use App\Models\Event;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\InAppMessageNotification;
use App\SubscriptionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('sends an email when a member registers for an event', function () {
    Notification::fake();

    $member = User::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'duration_days' => 365,
        'is_active' => true,
    ]);

    Subscription::factory()
        ->for($member)
        ->for($plan)
        ->create([
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(30),
        ]);

    $event = Event::query()->create([
        'title' => 'AMUHI Annual Meeting',
        'description' => 'Annual member gathering.',
        'location' => 'Jakarta',
        'starts_at' => now()->addDays(7),
        'ends_at' => now()->addDays(7)->addHours(2),
        'status' => EventStatus::Upcoming->value,
    ]);

    $this->actingAs($member)
        ->from(route('events.show', $event))
        ->post(route('events.register', $event))
        ->assertRedirect(route('events.show', $event))
        ->assertSessionHas('success');

    Notification::assertSentTo(
        $member,
        InAppMessageNotification::class,
        function (InAppMessageNotification $notification, array $channels): bool {
            expect($channels)->toContain('mail');
            expect($notification->title)->toBe('Registration confirmed');

            return true;
        }
    );

    $this->assertDatabaseHas('notifications', [
        'user_id' => $member->id,
        'type' => 'event',
        'title' => 'Registration confirmed',
    ]);
});
