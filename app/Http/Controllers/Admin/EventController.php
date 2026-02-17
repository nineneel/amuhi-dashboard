<?php

namespace App\Http\Controllers\Admin;

use App\EventStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventStoreRequest;
use App\Http\Requests\Admin\EventUpdateRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->orderBy('starts_at', 'desc')
            ->paginate(15);

        return view('admin.events.index', [
            'events' => $events,
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
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        Event::query()->create($data);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event created successfully.');
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
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
