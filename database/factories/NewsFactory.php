<?php

namespace Database\Factories;

use App\Enums\NewsStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->sentence(4));
        $slug = Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999);

        return [
            'slug' => $slug,
            'title' => $title,
            'summary' => fake()->paragraph(),
            'category' => fake()->randomElement(['Regulasi', 'Edukasi', 'Asosiasi', 'Layanan']),
            'tags' => fake()->randomElements([
                'Haji',
                'Umrah',
                'Regulasi',
                'Digitalisasi',
                'Pelayanan',
                'Jamaah',
            ], fake()->numberBetween(2, 4)),
            'badge' => fake()->optional()->randomElement(['Update Lapangan', 'Rilis Resmi', 'Sorotan']),
            'cover_image' => '/temp-images/news/'.Str::slug($title).'.jpg',
            'content' => [
                [
                    'type' => 'paragraph',
                    'text' => fake()->paragraph(3),
                ],
                [
                    'type' => 'heading',
                    'text' => fake()->sentence(4),
                ],
                [
                    'type' => 'list',
                    'items' => fake()->sentences(3),
                ],
                [
                    'type' => 'quote',
                    'text' => fake()->sentence(12),
                    'cite' => fake()->name(),
                ],
            ],
            'read_time_minutes' => fake()->numberBetween(4, 12),
            'author_name' => fake()->name(),
            'related_slugs' => fake()->optional()->randomElements([
                'strategi-perjalanan-aman',
                'panduan-layanan-jamaah-modern',
                'kolaborasi-industri-umrah',
            ], 2),
            'status' => NewsStatus::Draft,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => NewsStatus::Published,
            'published_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => NewsStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => NewsStatus::Archived,
            'published_at' => now()->subDays(fake()->numberBetween(31, 120)),
        ]);
    }
}
