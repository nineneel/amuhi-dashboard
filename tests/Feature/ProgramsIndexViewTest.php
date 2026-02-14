<?php

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the programs page with program cards for subscribed members', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get(route('programs.index'));

    $response->assertSuccessful();

    expect(substr_count($response->getContent(), 'data-testid="program-card"'))->toBe(6);

    $response->assertSeeText('AMUHI Academy');
    $response->assertSeeText('AMUHI Check');
    $response->assertSeeText('AMUHI Protect');
    $response->assertSeeText('AMUHI Care');
    $response->assertSeeText('AMUHI Network');
    $response->assertSeeText('AMUHI Digital');
});

