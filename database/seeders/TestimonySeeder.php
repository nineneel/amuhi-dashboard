<?php

namespace Database\Seeders;

use App\Models\Testimony;
use Illuminate\Database\Seeder;

class TestimonySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonies = [
            [
                'text' => 'Pendampingan AMUHI membantu kami meningkatkan kualitas layanan jamaah secara terukur.',
                'name' => 'Ahmad Fikri',
                'role' => 'Direktur Operasional, Safa Tour',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'text' => 'Kolaborasi antar anggota membuat proses edukasi jamaah menjadi jauh lebih efektif.',
                'name' => 'Nadia Rahma',
                'role' => 'Head of Service, Rahmah Travel',
                'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'text' => 'Program compliance clinic AMUHI memudahkan tim kami memahami perubahan regulasi.',
                'name' => 'Bima Saputra',
                'role' => 'Compliance Manager, Amanah Umrah',
                'video_url' => 'https://www.youtube.com/watch?v=3JZ_D3ELwOQ',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'text' => 'Standar layanan berbasis data memberi dampak nyata pada kepuasan jamaah.',
                'name' => 'Siti Marlina',
                'role' => 'CEO, Barokah Wisata',
                'video_url' => 'https://www.youtube.com/watch?v=L_jWHffIx5E',
                'sort_order' => 4,
                'is_active' => false,
            ],
        ];

        foreach ($testimonies as $testimony) {
            Testimony::query()->updateOrCreate(
                [
                    'name' => $testimony['name'],
                    'video_url' => $testimony['video_url'],
                ],
                [
                    'text' => $testimony['text'],
                    'role' => $testimony['role'],
                    'sort_order' => $testimony['sort_order'],
                    'is_active' => $testimony['is_active'],
                ]
            );
        }
    }
}
