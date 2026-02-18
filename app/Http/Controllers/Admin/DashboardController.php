<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
                    'title' => __('ui.admin.total_users'),
                    'value' => number_format(User::query()->count()),
                    'icon' => 'users',
                    'trend' => 8.4,
                ],
                [
                    'title' => __('ui.admin.events'),
                    'value' => number_format(Event::query()->count()),
                    'icon' => 'calendar',
                    'trend' => 3.1,
                ],
                [
                    'title' => __('ui.admin.active_subscriptions'),
                    'value' => number_format(Subscription::query()->where('status', SubscriptionStatus::Active->value)->count()),
                    'icon' => 'subscription',
                    'trend' => 5.2,
                ],
                [
                    'title' => __('ui.admin.published_news'),
                    'value' => number_format(News::query()->where('status', 'published')->count()),
                    'icon' => 'news',
                    'trend' => 0.0,
                ],
            ],
        ]);
    }
}
