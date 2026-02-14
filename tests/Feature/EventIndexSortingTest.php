<?php

use App\EventStatus;
use App\Models\Event;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sorts activities by nearest start date', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
    ]);

    Event::query()->create([
        'title' => 'Far Future',
        'description' => null,
        'location' => 'Jakarta',
        'image' => null,
        'starts_at' => now()->addDays(30),
        'ends_at' => now()->addDays(30)->addHours(2),
        'status' => EventStatus::Upcoming,
    ]);

    Event::query()->create([
        'title' => 'Past Two Days',
        'description' => null,
        'location' => 'Online',
        'image' => null,
        'starts_at' => now()->subDays(2),
        'ends_at' => now()->subDays(2)->addHours(2),
        'status' => EventStatus::Past,
    ]);

    Event::query()->create([
        'title' => 'Tomorrow',
        'description' => null,
        'location' => 'Online',
        'image' => null,
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHours(2),
        'status' => EventStatus::Upcoming,
    ]);

    Event::query()->create([
        'title' => 'Ongoing-ish',
        'description' => null,
        'location' => 'Online',
        'image' => null,
        'starts_at' => now()->subHour(),
        'ends_at' => now()->addHour(),
        'status' => EventStatus::Ongoing,
    ]);

    Event::query()->create([
        'title' => 'TBA',
        'description' => null,
        'location' => null,
        'image' => null,
        'starts_at' => null,
        'ends_at' => null,
        'status' => EventStatus::Upcoming,
    ]);

    $response = $this->actingAs($user)
        ->get(route('events.index'))
        ->assertSuccessful();

    $titles = $response->viewData('allEvents')->pluck('title')->all();

    expect($titles)->toBe([
        'Ongoing-ish',
        'Tomorrow',
        'Past Two Days',
        'Far Future',
        'TBA',
    ]);
});
