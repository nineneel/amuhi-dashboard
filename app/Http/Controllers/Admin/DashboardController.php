<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
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
                    'trend' => 8.4,
                ],
                [
                    'title' => 'Events',
                    'value' => number_format(Event::query()->count()),
                    'icon' => 'calendar',
                    'trend' => 3.1,
                ],
                [
                    'title' => 'Active Subscriptions',
                    'value' => number_format(Subscription::query()->where('status', SubscriptionStatus::Active->value)->count()),
                    'icon' => 'subscription',
                    'trend' => 5.2,
                ],
                [
                    'title' => 'Admin Accounts',
                    'value' => number_format(User::query()->whereIn('role', ['admin', 'super_admin'])->count()),
                    'icon' => 'shield',
                    'trend' => 0.0,
                ],
            ],
        ]);
    }
}
