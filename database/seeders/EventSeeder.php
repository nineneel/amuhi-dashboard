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
        $summitStart = now()->addDays(10)->setTime(9, 0);
        $summitEnd = $summitStart->copy()->addHours(6);

        $workshopStart = now()->addDays(3)->setTime(13, 30);
        $workshopEnd = $workshopStart->copy()->addHours(3);

        $networkStart = now()->subDay()->setTime(18, 0);
        $networkEnd = $networkStart->copy()->addDays(2)->setTime(21, 0);

        $webinarStart = now()->subDays(14)->setTime(10, 0);
        $webinarEnd = $webinarStart->copy()->addHours(2);

        $clinicStart = now()->addDays(20)->setTime(9, 30);
        $clinicEnd = $clinicStart->copy()->addHours(4);

        $bootcampStart = now()->addDays(32)->setTime(9, 0);
        $bootcampEnd = $bootcampStart->copy()->addHours(7);

        $events = [
            [
                'title' => 'AMUHI Summit 2026',
                'description' => 'Annual summit for AMUHI members to share updates, policy guidance, and growth opportunities.',
                'location' => 'Jakarta Convention Center',
                'starts_at' => $summitStart,
                'ends_at' => $summitEnd,
                'status' => EventStatus::Upcoming,
            ],
            [
                'title' => 'Regulatory Compliance Workshop',
                'description' => 'Hands-on workshop covering the latest compliance requirements and operational best practices.',
                'location' => 'AMUHI Training Hub, Bandung',
                'starts_at' => $workshopStart,
                'ends_at' => $workshopEnd,
                'status' => EventStatus::Upcoming,
            ],
            [
                'title' => 'Member Networking Night',
                'description' => 'Evening gathering to connect with fellow members, partners, and program leaders.',
                'location' => 'AMUHI Lounge, Jakarta',
                'starts_at' => $networkStart,
                'ends_at' => $networkEnd,
                'status' => EventStatus::Ongoing,
            ],
            [
                'title' => 'Safety & Risk Webinar',
                'description' => 'Online webinar focused on risk mitigation, safety standards, and audit readiness.',
                'location' => 'Online',
                'starts_at' => $webinarStart,
                'ends_at' => $webinarEnd,
                'status' => EventStatus::Past,
            ],
            [
                'title' => 'Operational Excellence Clinic',
                'description' => 'Small-group clinic with AMUHI advisors to review operational challenges and solutions.',
                'location' => 'AMUHI Office, Surabaya',
                'starts_at' => $clinicStart,
                'ends_at' => $clinicEnd,
                'status' => EventStatus::Cancelled,
            ],
            [
                'title' => 'Digital Tools Bootcamp',
                'description' => 'Training session for AMUHI digital tools, dashboards, and reporting workflows.',
                'location' => 'AMUHI Digital Lab, Jakarta',
                'starts_at' => $bootcampStart,
                'ends_at' => $bootcampEnd,
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
