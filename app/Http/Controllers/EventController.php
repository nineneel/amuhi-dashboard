<?php

namespace App\Http\Controllers;

use App\EventStatus;
use App\Models\ActivityLog;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = $request->query('filter', 'all');

        $query = Event::query()
            ->withCount(['eventRegistrations as registrations_count'])
            ->withExists([
                'eventRegistrations as is_registered' => function ($builder) use ($user) {
                    $builder->where('user_id', $user->id);
                },
            ])
            ->orderBy('starts_at');

        if ($filter === 'registered') {
            $query->whereHas('eventRegistrations', function ($builder) use ($user) {
                $builder->where('user_id', $user->id);
            });
        }

        $events = $query->get();

        return view('events.index', [
            'events' => $events,
            'filter' => $filter,
        ]);
    }

    public function calendar(Request $request): View
    {
        $user = $request->user();
        $events = Event::query()
            ->withExists([
                'eventRegistrations as is_registered' => function ($builder) use ($user) {
                    $builder->where('user_id', $user->id);
                },
            ])
            ->orderBy('starts_at')
            ->get();

        $calendarEvents = $events
            ->filter(fn (Event $event) => $event->starts_at !== null)
            ->map(function (Event $event) {
                $isRegistered = (bool) ($event->is_registered ?? false);
                $status = $event->status?->value ?? EventStatus::Upcoming->value;
                $accent = $isRegistered ? '#16a34a' : '#4f46e5';
                $endAt = $event->ends_at ?? $event->starts_at?->copy()->addHours(2);

                return [
                    'id' => (string) $event->id,
                    'title' => $event->title,
                    'body' => $event->description ?? '',
                    'location' => $event->location ?? '',
                    'start' => $event->starts_at?->toIso8601String(),
                    'end' => $endAt?->toIso8601String(),
                    'category' => 'time',
                    'isAllday' => false,
                    'bgColor' => $accent,
                    'borderColor' => $accent,
                    'color' => '#ffffff',
                    'raw' => [
                        'status' => $status,
                        'location' => $event->location,
                        'registered' => $isRegistered,
                    ],
                ];
            })
            ->values();

        return view('events.calendar', [
            'events' => $events,
            'calendarEvents' => $calendarEvents,
        ]);
    }

    public function show(Event $event, Request $request): View
    {
        $user = $request->user();
        $event->loadCount('eventRegistrations');
        $isRegistered = $event->eventRegistrations()
            ->where('user_id', $user->id)
            ->exists();

        return view('events.show', [
            'event' => $event,
            'isRegistered' => $isRegistered,
        ]);
    }

    public function register(Event $event, Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($event->status === EventStatus::Past) {
            return back()->with('warning', 'This event has already ended.');
        }

        if ($event->status === EventStatus::Cancelled) {
            return back()->with('warning', 'This event has been cancelled.');
        }

        $registration = $event->eventRegistrations()->firstOrCreate(
            ['user_id' => $user->id],
            ['registered_at' => now(), 'status' => 'registered']
        );

        if ($registration->wasRecentlyCreated) {
            ActivityLog::query()->create([
                'user_id' => $user->id,
                'action' => 'Event registration',
                'description' => 'You registered for '.$event->title.'.',
                'subject_type' => Event::class,
                'subject_id' => $event->id,
                'ip_address' => $request->ip(),
            ]);

            Notification::query()->create([
                'user_id' => $user->id,
                'type' => 'event',
                'title' => 'Registration confirmed',
                'message' => 'You are registered for '.$event->title.'.',
                'sent_via' => 'app',
            ]);
        }

        $message = $registration->wasRecentlyCreated
            ? 'You are registered for this event.'
            : 'You are already registered for this event.';

        return back()->with('success', $message);
    }
}
