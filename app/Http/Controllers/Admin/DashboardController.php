<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Event;
use App\Models\News;
use App\Models\Subscription;
use App\Models\User;
use App\SubscriptionStatus;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard.index', [
            'metrics' => [
                [
                    'title' => 'Total Users',
                    'value' => number_format(User::query()->count()),
                    'icon' => 'users',
                    'trend' => null,
                ],
                [
                    'title' => 'Total Events',
                    'value' => number_format(Event::query()->count()),
                    'icon' => 'calendar',
                    'trend' => null,
                ],
                [
                    'title' => 'Active Subscriptions',
                    'value' => number_format(Subscription::query()->where('status', SubscriptionStatus::Active->value)->count()),
                    'icon' => 'subscription',
                    'trend' => null,
                ],
                [
                    'title' => 'Published News',
                    'value' => number_format(News::query()->published()->count()),
                    'icon' => 'news',
                    'trend' => null,
                ],
            ],
            'recentActivities' => ActivityLog::query()
                ->with('user')
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}
