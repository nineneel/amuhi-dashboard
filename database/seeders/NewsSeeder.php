<?php

namespace Database\Seeders;

use App\Enums\NewsStatus;
use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newsItems = [
            [
                'slug' => 'regulasi-layanan-umrah-2026',
                'title' => 'Regulasi Layanan Umrah 2026 Resmi Diperbarui',
                'summary' => 'AMUHI merangkum poin penting pembaruan regulasi untuk memastikan standar layanan jamaah tetap aman, transparan, dan akuntabel.',
                'category' => 'Regulasi',
                'tags' => ['Regulasi', 'Umrah 2026', 'Kepatuhan'],
                'badge' => 'Rilis Resmi',
                'cover_image' => '/temp-images/news/regulasi-layanan-umrah-2026.jpg',
                'content' => [
                    ['type' => 'paragraph', 'text' => 'Pembaruan regulasi menekankan pentingnya transparansi biaya, kejelasan jadwal perjalanan, dan perlindungan jamaah.'],
                    ['type' => 'heading', 'text' => 'Tiga Fokus Utama'],
                    ['type' => 'list', 'items' => ['Standar kontrak layanan', 'Audit operasional berkala', 'Peningkatan kanal pengaduan']],
                    ['type' => 'quote', 'text' => 'Kepatuhan adalah fondasi kepercayaan publik.', 'cite' => 'Tim Kebijakan AMUHI'],
                ],
                'read_time_minutes' => 6,
                'author_name' => 'AMUHI Editorial',
                'related_slugs' => ['panduan-perlindungan-konsumen-umrah', 'transformasi-digital-manajemen-jamaah'],
                'status' => NewsStatus::Published,
                'published_at' => now()->subDays(12),
            ],
            [
                'slug' => 'transformasi-digital-manajemen-jamaah',
                'title' => 'Transformasi Digital Manajemen Jamaah Meningkatkan Ketepatan Layanan',
                'summary' => 'Implementasi sistem digital membantu anggota AMUHI mengelola data jamaah, jadwal, dan komunikasi secara lebih efisien.',
                'category' => 'Teknologi',
                'tags' => ['Digitalisasi', 'Operasional', 'Layanan Jamaah'],
                'badge' => 'Update Lapangan',
                'cover_image' => '/temp-images/news/transformasi-digital-manajemen-jamaah.jpg',
                'content' => [
                    ['type' => 'paragraph', 'text' => 'Penerapan dashboard operasional menurunkan waktu respons layanan dan meningkatkan ketepatan informasi perjalanan.'],
                    ['type' => 'heading', 'text' => 'Manfaat untuk Anggota'],
                    ['type' => 'list', 'items' => ['Pemantauan real-time', 'Integrasi data keberangkatan', 'Riwayat layanan jamaah terpusat']],
                    ['type' => 'quote', 'text' => 'Teknologi mempercepat pelayanan tanpa mengurangi kualitas interaksi manusia.', 'cite' => 'Divisi Inovasi AMUHI'],
                ],
                'read_time_minutes' => 7,
                'author_name' => 'Tim Teknologi AMUHI',
                'related_slugs' => ['regulasi-layanan-umrah-2026'],
                'status' => NewsStatus::Published,
                'published_at' => now()->subDays(8),
            ],
            [
                'slug' => 'kemitraan-strategis-amuhi-dan-maskapai',
                'title' => 'Kemitraan Strategis AMUHI dan Maskapai Perkuat Kualitas Perjalanan',
                'summary' => 'Kolaborasi baru difokuskan pada reliabilitas jadwal, peningkatan kenyamanan, dan mitigasi risiko operasional.',
                'category' => 'Asosiasi',
                'tags' => ['Kemitraan', 'Maskapai', 'Perjalanan'],
                'badge' => 'Sorotan',
                'cover_image' => '/temp-images/news/kemitraan-strategis-amuhi-dan-maskapai.jpg',
                'content' => [
                    ['type' => 'paragraph', 'text' => 'Kolaborasi ini membuka akses koordinasi yang lebih cepat untuk penanganan perubahan jadwal dan kebutuhan jamaah di lapangan.'],
                    ['type' => 'heading', 'text' => 'Ruang Kolaborasi'],
                    ['type' => 'list', 'items' => ['SLA penanganan perubahan jadwal', 'Pembaruan informasi keberangkatan', 'Koordinasi layanan prioritas']],
                    ['type' => 'quote', 'text' => 'Kemitraan kuat menghadirkan kepastian layanan yang lebih baik.', 'cite' => 'Sekretariat AMUHI'],
                ],
                'read_time_minutes' => 5,
                'author_name' => 'Sekretariat AMUHI',
                'related_slugs' => ['transformasi-digital-manajemen-jamaah'],
                'status' => NewsStatus::Published,
                'published_at' => now()->subDays(5),
            ],
            [
                'slug' => 'panduan-perlindungan-konsumen-umrah',
                'title' => 'Panduan Perlindungan Konsumen Umrah untuk Anggota dan Jamaah',
                'summary' => 'Dokumen panduan baru menyoroti praktik terbaik pencegahan risiko, edukasi kontrak, dan mekanisme pendampingan jamaah.',
                'category' => 'Edukasi',
                'tags' => ['Perlindungan Konsumen', 'Edukasi', 'Umrah'],
                'badge' => null,
                'cover_image' => '/temp-images/news/panduan-perlindungan-konsumen-umrah.jpg',
                'content' => [
                    ['type' => 'paragraph', 'text' => 'Panduan ini dirancang untuk membantu anggota memastikan seluruh proses layanan berjalan sesuai standar perlindungan jamaah.'],
                    ['type' => 'heading', 'text' => 'Komponen Panduan'],
                    ['type' => 'list', 'items' => ['Checklist kontrak', 'SOP komunikasi', 'Mekanisme mediasi awal']],
                    ['type' => 'quote', 'text' => 'Perlindungan konsumen harus hadir sejak awal proses layanan.', 'cite' => 'Komite Etik AMUHI'],
                ],
                'read_time_minutes' => 8,
                'author_name' => 'Komite Etik AMUHI',
                'related_slugs' => ['regulasi-layanan-umrah-2026'],
                'status' => NewsStatus::Draft,
                'published_at' => null,
            ],
            [
                'slug' => 'evaluasi-musim-haji-1447h',
                'title' => 'Evaluasi Musim Haji 1447H: Pembelajaran dan Rekomendasi',
                'summary' => 'Laporan evaluasi menyoroti indikator layanan, tantangan lapangan, serta rekomendasi peningkatan untuk musim berikutnya.',
                'category' => 'Laporan',
                'tags' => ['Haji 1447H', 'Evaluasi', 'Rekomendasi'],
                'badge' => 'Arsip',
                'cover_image' => '/temp-images/news/evaluasi-musim-haji-1447h.jpg',
                'content' => [
                    ['type' => 'paragraph', 'text' => 'Evaluasi ini merangkum data operasional dari berbagai daerah untuk memperkuat standar layanan nasional.'],
                    ['type' => 'heading', 'text' => 'Temuan Kunci'],
                    ['type' => 'list', 'items' => ['Perluasan pelatihan petugas', 'Sinkronisasi komunikasi lintas tim', 'Standardisasi dokumentasi layanan']],
                    ['type' => 'quote', 'text' => 'Setiap musim haji adalah ruang belajar untuk layanan yang lebih baik.', 'cite' => 'Tim Evaluasi AMUHI'],
                ],
                'read_time_minutes' => 9,
                'author_name' => 'Tim Evaluasi AMUHI',
                'related_slugs' => null,
                'status' => NewsStatus::Archived,
                'published_at' => now()->subMonths(8),
            ],
        ];

        foreach ($newsItems as $newsItem) {
            News::query()->updateOrCreate(
                ['slug' => $newsItem['slug']],
                [
                    'title' => $newsItem['title'],
                    'summary' => $newsItem['summary'],
                    'category' => $newsItem['category'],
                    'tags' => $newsItem['tags'],
                    'badge' => $newsItem['badge'],
                    'cover_image' => $newsItem['cover_image'],
                    'content' => $newsItem['content'],
                    'read_time_minutes' => $newsItem['read_time_minutes'],
                    'author_name' => $newsItem['author_name'],
                    'related_slugs' => $newsItem['related_slugs'],
                    'status' => $newsItem['status'],
                    'published_at' => $newsItem['published_at'],
                ]
            );
        }
    }
}
