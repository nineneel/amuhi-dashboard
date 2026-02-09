<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->get();

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function markRead(Notification $notification, Request $request): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(404);
        }

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        return back()->with('success', 'Notification marked as read.');
    }
}
