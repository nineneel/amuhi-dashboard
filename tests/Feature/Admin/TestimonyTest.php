<?php

use App\Models\Testimony;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows testimonies index to admin', function () {
    $admin = User::factory()->admin()->create();
    Testimony::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('admin.testimonies.index'))
        ->assertSuccessful();
});

it('shows testimony create form', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.testimonies.create'))
        ->assertSuccessful();
});

it('can create a testimony', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.testimonies.store'), [
            'text' => 'Great platform!',
            'name' => 'John User',
            'role' => 'Student',
            'video_url' => 'https://youtube.com/watch?v=abc123',
            'is_active' => true,
        ])
        ->assertRedirect(route('admin.testimonies.index'));

    expect(Testimony::query()->where('name', 'John User')->exists())->toBeTrue();
});

it('validates required fields on testimony store', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.testimonies.store'), [])
        ->assertSessionHasErrors(['text', 'name', 'role', 'video_url']);
});

it('shows testimony edit form', function () {
    $admin = User::factory()->admin()->create();
    $testimony = Testimony::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.testimonies.edit', $testimony))
        ->assertSuccessful();
});

it('can update a testimony', function () {
    $admin = User::factory()->admin()->create();
    $testimony = Testimony::factory()->create(['name' => 'Old Name']);

    $this->actingAs($admin)
        ->put(route('admin.testimonies.update', $testimony), [
            'text' => $testimony->text,
            'name' => 'New Name',
            'role' => $testimony->role,
            'video_url' => $testimony->video_url,
            'is_active' => $testimony->is_active,
        ])
        ->assertRedirect(route('admin.testimonies.index'));

    expect($testimony->fresh()->name)->toBe('New Name');
});

it('can delete a testimony', function () {
    $admin = User::factory()->admin()->create();
    $testimony = Testimony::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.testimonies.destroy', $testimony))
        ->assertRedirect(route('admin.testimonies.index'));

    $this->assertModelMissing($testimony);
});

it('can reorder testimonies', function () {
    $admin = User::factory()->admin()->create();
    $t1 = Testimony::factory()->create(['sort_order' => 0]);
    $t2 = Testimony::factory()->create(['sort_order' => 1]);
    $t3 = Testimony::factory()->create(['sort_order' => 2]);

    $this->actingAs($admin)
        ->post(route('admin.testimonies.reorder'), [
            'order' => [$t3->id, $t1->id, $t2->id],
        ])
        ->assertSuccessful()
        ->assertJson(['success' => true]);

    expect($t3->fresh()->sort_order)->toBe(0)
        ->and($t1->fresh()->sort_order)->toBe(1)
        ->and($t2->fresh()->sort_order)->toBe(2);
});
