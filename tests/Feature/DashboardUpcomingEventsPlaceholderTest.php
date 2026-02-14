<?php

use App\EventStatus;
use App\Models\Event;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses the AMUHI logo as placeholder when an upcoming event has no image on the dashboard', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
    ]);

    Event::query()->create([
        'title' => 'No Image Event',
        'description' => null,
        'location' => 'Jakarta',
        'image' => null,
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHours(2),
        'status' => EventStatus::Upcoming,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('src="/images/logo/logo-icon.svg"', false)
        ->assertSee(route('events.index'), false)
        ->assertSee('inline-flex items-center justify-center rounded-lg border border-gray-300', false);
});
