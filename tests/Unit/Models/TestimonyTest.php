<?php

use App\Models\Testimony;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('has the expected fillable attributes', function () {
    $testimony = new Testimony();

    expect($testimony->getFillable())->toBe([
        'text',
        'name',
        'role',
        'video_url',
        'sort_order',
        'is_active',
    ]);
});

it('filters active testimonies and orders by sort order', function () {
    $activeHighOrder = Testimony::factory()->active()->create(['sort_order' => 5]);
    $activeLowOrder = Testimony::factory()->active()->create(['sort_order' => 2]);
    Testimony::factory()->inactive()->create(['sort_order' => 1]);

    $activeOrderedIds = Testimony::query()
        ->active()
        ->ordered()
        ->pluck('id')
        ->all();

    expect($activeOrderedIds)->toBe([
        $activeLowOrder->id,
        $activeHighOrder->id,
    ]);
});

it('creates a valid model using factory states', function () {
    $active = Testimony::factory()->active()->create();
    $inactive = Testimony::factory()->inactive()->create();

    expect($active->is_active)->toBeTrue()
        ->and($inactive->is_active)->toBeFalse()
        ->and($active->video_url)->toStartWith('https://www.youtube.com/watch?v=');
});
