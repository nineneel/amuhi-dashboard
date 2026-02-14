<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects an unpaid member to payment when opening dashboard', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('payments.show'))
        ->assertSessionHas('warning');
});

it('redirects an unpaid member to payment when opening events', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('events.index'))
        ->assertRedirect(route('payments.show'))
        ->assertSessionHas('warning');
});

it('redirects an unpaid member to payment when opening programs', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('programs.index'))
        ->assertRedirect(route('payments.show'))
        ->assertSessionHas('warning');
});
