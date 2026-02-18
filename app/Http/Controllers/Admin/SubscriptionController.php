<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $perPage = (int) $request->query('per_page', 15);

        $subscriptions = Subscription::query()
            ->with(['user', 'subscriptionPlan'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('user', function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.subscriptions.index', [
            'subscriptions' => $subscriptions,
        ]);
    }

    public function show(Subscription $subscription): View
    {
        $subscription->load(['user', 'subscriptionPlan', 'invoices.payments']);

        return view('admin.subscriptions.show', [
            'subscription' => $subscription,
        ]);
    }
}
