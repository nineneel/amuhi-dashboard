<?php

use App\Enums\NewsStatus;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function newsPayload(array $overrides = []): array
{
    return array_merge([
        'slug' => 'new-policy-update',
        'title' => 'New Policy Update',
        'summary' => 'Summary for policy update.',
        'category' => 'Regulasi',
        'tags' => ['Policy'],
        'content' => [
            [
                'type' => 'paragraph',
                'text' => 'Policy details paragraph.',
            ],
        ],
        'author_name' => 'Admin User',
        'related_slugs' => ['related-story'],
        'status' => NewsStatus::Draft->value,
    ], $overrides);
}

it('supports creating, updating, and deleting news', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.news.store'), newsPayload())
        ->assertRedirect();

    $news = News::query()->where('slug', 'new-policy-update')->first();

    expect($news)->not->toBeNull();

    $this->actingAs($admin)
        ->put(route('admin.news.update', $news), newsPayload([
            'slug' => 'policy-updated',
            'title' => 'Policy Updated',
        ]))
        ->assertRedirect(route('admin.news.edit', $news));

    $news->refresh();
    expect($news->slug)->toBe('policy-updated');
    expect($news->title)->toBe('Policy Updated');

    $this->actingAs($admin)
        ->delete(route('admin.news.destroy', $news))
        ->assertRedirect(route('admin.news.index'));

    $this->assertModelMissing($news);
});

it('supports publish and unpublish actions', function () {
    $admin = User::factory()->admin()->create();
    $news = News::factory()->draft()->create();

    $this->actingAs($admin)
        ->post(route('admin.news.publish', $news))
        ->assertRedirect(route('admin.news.index'));

    $news->refresh();

    expect($news->status)->toBe(NewsStatus::Published);
    expect($news->published_at)->not->toBeNull();

    $this->actingAs($admin)
        ->post(route('admin.news.unpublish', $news))
        ->assertRedirect(route('admin.news.index'));

    $news->refresh();

    expect($news->status)->toBe(NewsStatus::Draft);
    expect($news->published_at)->toBeNull();
});

it('returns validation errors for invalid payloads', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.news.store'), [
            'slug' => '',
            'title' => '',
            'summary' => '',
            'category' => '',
            'content' => [],
            'author_name' => '',
        ])
        ->assertSessionHasErrors([
            'slug',
            'title',
            'summary',
            'category',
            'content',
            'author_name',
        ]);
});
