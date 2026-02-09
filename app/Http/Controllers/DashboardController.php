<?php

namespace App\Http\Controllers;

use App\EventStatus;
use App\InvoiceStatus;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $upcomingEvents = Event::query()
            ->withCount('eventRegistrations')
            ->withExists([
                'eventRegistrations as is_registered' => function ($builder) use ($user) {
                    $builder->where('user_id', $user->id);
                },
            ])
            ->whereIn('status', [EventStatus::Upcoming, EventStatus::Ongoing])
            ->orderBy('starts_at')
            ->take(4)
            ->get();

        $nextEvent = $upcomingEvents->first();

        $recentInvoices = $user->invoices()
            ->with('subscription.subscriptionPlan')
            ->latest()
            ->take(4)
            ->get();

        $recentActivity = $user->activityLogs()
            ->latest()
            ->take(5)
            ->get();

        $recentNotifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'registered_events' => $user->eventRegistrations()->count(),
            'open_invoices' => $user->invoices()
                ->whereIn('status', [InvoiceStatus::Pending, InvoiceStatus::Overdue])
                ->count(),
            'upcoming_events' => Event::query()
                ->whereIn('status', [EventStatus::Upcoming, EventStatus::Ongoing])
                ->count(),
            'unread_notifications' => $user->notifications()
                ->whereNull('read_at')
                ->count(),
        ];

        $subscription = $user->currentSubscription();

        return view('dashboard.index', [
            'user' => $user,
            'subscription' => $subscription,
            'stats' => $stats,
            'upcomingEvents' => $upcomingEvents,
            'nextEvent' => $nextEvent,
            'recentInvoices' => $recentInvoices,
            'recentActivity' => $recentActivity,
            'recentNotifications' => $recentNotifications,
        ]);
    }
}
