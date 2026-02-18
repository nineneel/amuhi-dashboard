<?php

use App\Enums\NewsStatus;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows news index to admin', function () {
    $admin = User::factory()->admin()->create();
    News::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('admin.news.index'))
        ->assertSuccessful();
});

it('shows news create form', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.news.create'))
        ->assertSuccessful();
});

it('can create a news article', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.news.store'), [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'summary' => 'A brief summary.',
            'category' => 'Technology',
            'content' => [['type' => 'paragraph', 'text' => 'Body content.']],
            'author_name' => 'Jane Doe',
        ])
        ->assertRedirect(route('admin.news.index'));

    expect(News::query()->where('slug', 'test-article')->exists())->toBeTrue();
});

it('validates required fields on news store', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.news.store'), [])
        ->assertSessionHasErrors(['title', 'slug', 'summary', 'category', 'content', 'author_name']);
});

it('validates unique slug on store', function () {
    $admin = User::factory()->admin()->create();
    News::factory()->create(['slug' => 'existing-slug']);

    $this->actingAs($admin)
        ->post(route('admin.news.store'), [
            'title' => 'Another',
            'slug' => 'existing-slug',
            'summary' => 'Summary',
            'category' => 'Tech',
            'content' => [['type' => 'paragraph', 'text' => 'Text']],
            'author_name' => 'Author',
        ])
        ->assertSessionHasErrors('slug');
});

it('shows news edit form', function () {
    $admin = User::factory()->admin()->create();
    $news = News::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.news.edit', $news))
        ->assertSuccessful();
});

it('can update a news article', function () {
    $admin = User::factory()->admin()->create();
    $news = News::factory()->create(['title' => 'Old Title']);

    $this->actingAs($admin)
        ->put(route('admin.news.update', $news), [
            'title' => 'Updated Title',
            'slug' => $news->slug,
            'summary' => $news->summary,
            'category' => $news->category,
            'content' => $news->content,
            'author_name' => $news->author_name,
        ])
        ->assertRedirect(route('admin.news.index'));

    expect($news->fresh()->title)->toBe('Updated Title');
});

it('can delete a news article', function () {
    $admin = User::factory()->admin()->create();
    $news = News::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.news.destroy', $news))
        ->assertRedirect(route('admin.news.index'));

    $this->assertModelMissing($news);
});

it('can publish a news article', function () {
    $admin = User::factory()->admin()->create();
    $news = News::factory()->draft()->create();

    $this->actingAs($admin)
        ->post(route('admin.news.publish', $news))
        ->assertRedirect();

    expect($news->fresh()->status)->toBe(NewsStatus::Published)
        ->and($news->fresh()->published_at)->not->toBeNull();
});

it('can unpublish a news article', function () {
    $admin = User::factory()->admin()->create();
    $news = News::factory()->published()->create();

    $this->actingAs($admin)
        ->post(route('admin.news.unpublish', $news))
        ->assertRedirect();

    expect($news->fresh()->status)->toBe(NewsStatus::Draft)
        ->and($news->fresh()->published_at)->toBeNull();
});
