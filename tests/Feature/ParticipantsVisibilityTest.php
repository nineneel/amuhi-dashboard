<?php

use App\EventStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('hides the participants label when fewer than 3 participants have registered', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $user->settings()->updateOrCreate(
        ['user_id' => $user->id],
        [
            'language' => 'id',
            'theme' => 'dark',
            'notification_email' => true,
            'notification_app' => true,
            'privacy_settings' => [],
        ]
    );

    Subscription::factory()->create([
        'user_id' => $user->id,
    ]);

    $hiddenEvent = Event::query()->create([
        'title' => 'Hidden Participants',
        'description' => null,
        'location' => 'Online',
        'image' => null,
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHour(),
        'status' => EventStatus::Upcoming,
    ]);

    $shownEvent = Event::query()->create([
        'title' => 'Shown Participants',
        'description' => null,
        'location' => 'Online',
        'image' => null,
        'starts_at' => now()->addDays(2),
        'ends_at' => now()->addDays(2)->addHour(),
        'status' => EventStatus::Upcoming,
    ]);

    $registrants = User::factory()->count(3)->create();

    EventRegistration::query()->create([
        'user_id' => $registrants[0]->id,
        'event_id' => $hiddenEvent->id,
        'registered_at' => now(),
        'status' => 'registered',
    ]);

    EventRegistration::query()->create([
        'user_id' => $registrants[1]->id,
        'event_id' => $hiddenEvent->id,
        'registered_at' => now(),
        'status' => 'registered',
    ]);

    EventRegistration::query()->create([
        'user_id' => $registrants[0]->id,
        'event_id' => $shownEvent->id,
        'registered_at' => now(),
        'status' => 'registered',
    ]);

    EventRegistration::query()->create([
        'user_id' => $registrants[1]->id,
        'event_id' => $shownEvent->id,
        'registered_at' => now(),
        'status' => 'registered',
    ]);

    EventRegistration::query()->create([
        'user_id' => $registrants[2]->id,
        'event_id' => $shownEvent->id,
        'registered_at' => now(),
        'status' => 'registered',
    ]);

    $dashboard = $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful();

    $dashboard->assertDontSee('2 peserta');
    $dashboard->assertSee('3 peserta');

    $eventsIndex = $this->actingAs($user)
        ->get(route('events.index'))
        ->assertSuccessful();

    $eventsIndex->assertDontSee('2 peserta');
    $eventsIndex->assertSee('3 peserta');

});
