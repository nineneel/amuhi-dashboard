<?php

use App\Models\Testimony;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function testimonyPayload(array $overrides = []): array
{
    return array_merge([
        'text' => 'This platform improved our operations.',
        'name' => 'Aisyah Rahman',
        'role' => 'Director',
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'sort_order' => 1,
        'is_active' => true,
    ], $overrides);
}

it('supports creating, updating, and deleting testimonies', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.testimonies.store'), testimonyPayload())
        ->assertRedirect();

    $testimony = Testimony::query()->where('name', 'Aisyah Rahman')->first();

    expect($testimony)->not->toBeNull();

    $this->actingAs($admin)
        ->put(route('admin.testimonies.update', $testimony), testimonyPayload([
            'name' => 'Rahman Updated',
            'sort_order' => 4,
            'is_active' => false,
        ]))
        ->assertRedirect(route('admin.testimonies.edit', $testimony));

    $testimony->refresh();

    expect($testimony->name)->toBe('Rahman Updated');
    expect($testimony->sort_order)->toBe(4);
    expect($testimony->is_active)->toBeFalse();

    $this->actingAs($admin)
        ->delete(route('admin.testimonies.destroy', $testimony))
        ->assertRedirect(route('admin.testimonies.index'));

    $this->assertModelMissing($testimony);
});

it('reorders testimonies', function () {
    $admin = User::factory()->admin()->create();

    $first = Testimony::factory()->create(['sort_order' => 1]);
    $second = Testimony::factory()->create(['sort_order' => 2]);

    $this->actingAs($admin)
        ->post(route('admin.testimonies.reorder'), [
            'orders' => [
                ['id' => $first->id, 'sort_order' => 10],
                ['id' => $second->id, 'sort_order' => 5],
            ],
        ])
        ->assertRedirect(route('admin.testimonies.index'));

    expect($first->fresh()->sort_order)->toBe(10);
    expect($second->fresh()->sort_order)->toBe(5);
});
