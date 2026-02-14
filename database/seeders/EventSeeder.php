<?php

namespace Database\Seeders;

use App\EventStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ticketFraudStart = now()->setDate(2025, 2, 8)->setTime(9, 0);
        $ticketFraudEnd = $ticketFraudStart->copy()->addHours(2);

        $mukernasStart = now()->setDate(2025, 3, 8)->setTime(9, 0);
        $mukernasEnd = $mukernasStart->copy()->addHours(6);

        $hajiStart = now()->setDate(2025, 4, 8)->setTime(9, 0);
        $hajiEnd = $hajiStart->copy()->addHours(3);

        $events = [
            [
                'title' => 'Penipuan Tiket',
                'description' => 'Webinar edukasi gratis membahas secara mendalam berbagai modus penipuan tiket umrah dan haji, cara mengidentifikasi travel agent palsu, serta langkah preventif untuk melindungi diri dan keluarga.',
                'location' => 'Online',
                'image' => '/temp-images/penipuan-tiket.jpeg',
                'starts_at' => $ticketFraudStart,
                'ends_at' => $ticketFraudEnd,
                'status' => EventStatus::Past,
            ],
            [
                'title' => 'Mukernas 1 Topics',
                'description' => 'Musyawarah Kerja Nasional perdana membahas roadmap strategis AMUHI, evaluasi program berjalan, serta perumusan kebijakan dan inisiatif baru untuk meningkatkan kualitas layanan industri umrah dan haji Indonesia.',
                'location' => 'Jakarta',
                'image' => '/temp-images/mukernas-1.jpeg',
                'starts_at' => $mukernasStart,
                'ends_at' => $mukernasEnd,
                'status' => EventStatus::Upcoming,
            ],
            [
                'title' => 'Haji 1448H',
                'description' => 'Sesi persiapan komprehensif untuk jamaah haji tahun 1448H mencakup panduan teknis keberangkatan, tips kesehatan dan kesiapan fisik, manajemen keuangan, serta pembahasan regulasi terbaru dari Kementerian Agama.',
                'location' => 'Jakarta',
                'image' => null,
                'starts_at' => $hajiStart,
                'ends_at' => $hajiEnd,
                'status' => EventStatus::Upcoming,
            ],
        ];

        $createdEvents = collect();

        foreach ($events as $event) {
            $createdEvents->push(Event::query()->updateOrCreate(
                ['title' => $event['title']],
                [
                    'description' => $event['description'],
                    'location' => $event['location'],
                    'image' => $event['image'],
                    'starts_at' => $event['starts_at'],
                    'ends_at' => $event['ends_at'],
                    'status' => $event['status'],
                ]
            ));
        }

        $user = User::query()->where('email', 'test@example.com')->first();

        if (! $user) {
            return;
        }

        $registrations = $createdEvents
            ->filter(fn (Event $event) => in_array($event->status, [EventStatus::Upcoming, EventStatus::Ongoing], true))
            ->take(2);

        foreach ($registrations as $event) {
            EventRegistration::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                ],
                [
                    'registered_at' => now(),
                    'status' => 'registered',
                ]
            );
        }
    }
}
