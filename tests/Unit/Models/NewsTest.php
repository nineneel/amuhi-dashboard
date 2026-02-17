<?php

use App\Enums\NewsStatus;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('has the expected fillable attributes', function () {
    $news = new News();

    expect($news->getFillable())->toBe([
        'slug',
        'title',
        'summary',
        'category',
        'tags',
        'badge',
        'cover_image',
        'content',
        'read_time_minutes',
        'author_name',
        'related_slugs',
        'status',
        'published_at',
    ]);
});

it('casts attributes correctly', function () {
    $publishedAt = now()->subDay();

    $news = News::factory()->create([
        'tags' => ['Haji', 'Regulasi'],
        'content' => [
            ['type' => 'paragraph', 'text' => 'Konten utama'],
            ['type' => 'list', 'items' => ['Satu', 'Dua']],
        ],
        'related_slugs' => ['berita-terkait-a', 'berita-terkait-b'],
        'status' => NewsStatus::Published,
        'published_at' => $publishedAt,
        'read_time_minutes' => 9,
    ]);

    expect($news->tags)->toBeArray()
        ->and($news->content)->toBeArray()
        ->and($news->related_slugs)->toBeArray()
        ->and($news->status)->toBe(NewsStatus::Published)
        ->and($news->published_at?->toDateTimeString())->toBe($publishedAt->toDateTimeString())
        ->and($news->read_time)->toBe('9 min read');
});

it('filters records by scope', function () {
    News::factory()->published()->create();
    News::factory()->draft()->create();
    News::factory()->archived()->create();
    News::factory()->create([
        'status' => NewsStatus::Published,
        'published_at' => now()->addDay(),
    ]);

    expect(News::query()->published()->count())->toBe(1)
        ->and(News::query()->draft()->count())->toBe(1)
        ->and(News::query()->archived()->count())->toBe(1);
});

it('creates a valid model using the factory states', function () {
    $publishedNews = News::factory()->published()->create();
    $draftNews = News::factory()->draft()->create();
    $archivedNews = News::factory()->archived()->create();

    expect($publishedNews->isPublished())->toBeTrue()
        ->and($draftNews->status)->toBe(NewsStatus::Draft)
        ->and($draftNews->published_at)->toBeNull()
        ->and($archivedNews->status)->toBe(NewsStatus::Archived);
});
