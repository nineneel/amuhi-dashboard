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
        $baseQuery = Event::query()
            ->withCount(['eventRegistrations as registrations_count'])
            ->withExists([
                'eventRegistrations as is_registered' => function ($builder) use ($user) {
                    $builder->where('user_id', $user->id);
                },
            ])
            ->orderByRaw('starts_at is null')
            ->orderBy('starts_at');

        $allEvents = (clone $baseQuery)->get();
        $registeredEvents = (clone $baseQuery)
            ->whereHas('eventRegistrations', function ($builder) use ($user) {
                $builder->where('user_id', $user->id);
            })
            ->get();

        return view('events.index', [
            'allEvents' => $allEvents,
            'registeredEvents' => $registeredEvents,
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
            ->orderByRaw('starts_at is null')
            ->orderBy('starts_at')
            ->get();

        return view('events.calendar', [
            'events' => $events,
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
