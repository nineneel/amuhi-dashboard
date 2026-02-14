<?php

use App\EventStatus;
use App\Models\Event;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds the expected events', function () {
    $this->seed(EventSeeder::class);

    $expectedEvents = [
        'Rapat Koordinasi AMUHI 2026' => [EventStatus::Upcoming, null],
        'Workshop Audit Kepatuhan' => [EventStatus::Ongoing, null],
        'Penipuan Tiket' => [EventStatus::Past, '/temp-images/penipuan-tiket.jpeg'],
        'Mukernas 1 Topics' => [EventStatus::Past, '/temp-images/mukernas-1.jpeg'],
        'Haji 1448H' => [EventStatus::Past, null],
        'Klinik Operasional Anggota' => [EventStatus::Cancelled, null],
    ];

    foreach ($expectedEvents as $title => [$status, $image]) {
        $event = Event::query()->where('title', $title)->first();

        expect($event)->not->toBeNull();
        expect($event->status)->toBe($status);
        expect($event->image)->toBe($image);
    }

    expect(Event::query()->where('status', EventStatus::Upcoming->value)->count())->toBeGreaterThan(0);
});

it('uses "Aktifitas" for event translations in Indonesian', function () {
    app()->setLocale('id');

    expect(__('ui.nav.events'))->toBe('Aktifitas');
    expect(__('ui.events.title'))->toBe('Aktifitas');
    expect(__('ui.events.calendar_title'))->toContain('Aktifitas');
    expect(__('ui.dashboard.upcoming_events'))->toContain('Aktifitas');

    expect(__('ui.events.title'))->not->toContain('Acara');
});
