<?php

namespace App\Http\Controllers\Admin;

use App\EventStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventStoreRequest;
use App\Http\Requests\Admin\EventUpdateRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $events = Event::query()
            ->withCount('eventRegistrations')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->orderByDesc('starts_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.events.index', [
            'events' => $events,
            'statuses' => EventStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.events.create', [
            'statuses' => EventStatus::cases(),
        ]);
    }

    public function store(EventStoreRequest $request): RedirectResponse
    {
        $event = Event::query()->create($request->validated());

        return redirect()
            ->route('admin.events.edit', $event)
            ->with('status', 'Event created successfully.');
    }

    public function show(Event $event): View
    {
        $event->load('eventRegistrations.user');

        return view('admin.events.show', [
            'event' => $event,
        ]);
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', [
            'event' => $event,
            'statuses' => EventStatus::cases(),
        ]);
    }

    public function update(EventUpdateRequest $request, Event $event): RedirectResponse
    {
        $event->update($request->validated());

        return redirect()
            ->route('admin.events.edit', $event)
            ->with('status', 'Event updated successfully.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('status', 'Event deleted successfully.');
    }
}
