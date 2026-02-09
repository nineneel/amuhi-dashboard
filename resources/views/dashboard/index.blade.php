@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('page-header')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Dashboard</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Dashboard</li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card stretch">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row align-items-start justify-content-between gap-4">
                        <div>
                            <p class="text-muted mb-2">Welcome back</p>
                            <h4 class="mb-2">{{ $user->name }}</h4>
                            <p class="text-muted mb-0 fs-13">
                                @if($subscription && $user->hasActiveSubscription())
                                    Your {{ $subscription->subscriptionPlan?->name ?? 'membership' }} is active.
                                    @if($subscription->ends_at)
                                        Next renewal: {{ $subscription->ends_at->format('d M Y') }}.
                                    @endif
                                @else
                                    You are currently on the free plan. Upgrade to unlock all AMUHI programs.
                                @endif
                            </p>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            @if($user->hasActiveSubscription())
                                <span class="badge bg-soft-success text-success d-flex align-items-center justify-content-center px-3 py-2 text-center align-self-center">
                                    Active Member
                                </span>
                            @else
                                <a href="{{ route('payments.show') }}" class="btn btn-primary">
                                    <i class="feather-credit-card me-1"></i>Upgrade Now
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="mb-0">Next Event</h6>
                            <a href="{{ route('events.index') }}" class="btn btn-sm btn-light">View All</a>
                        </div>
                        @if($nextEvent)
                            <div class="dashboard-next-event-card border rounded p-3">
                                <div class="d-flex flex-column flex-md-row align-items-start gap-3">
                                    <div class="avatar-text bg-soft-primary text-primary">
                                        <i class="feather-calendar"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $nextEvent->title }}</h6>
                                        <p class="text-muted fs-13 mb-2">
                                            {{ $nextEvent->description ? \Illuminate\Support\Str::limit($nextEvent->description, 120) : 'Event details will be announced soon.' }}
                                        </p>
                                        <div class="d-flex flex-wrap gap-3 fs-13 text-muted">
                                            <span><i class="feather-calendar me-1"></i>{{ $nextEvent->starts_at?->format('d M Y, H:i') ?? 'Date TBA' }}</span>
                                            <span><i class="feather-map-pin me-1"></i>{{ $nextEvent->location ?? 'Location TBA' }}</span>
                                            <span><i class="feather-users me-1"></i>{{ $nextEvent->event_registrations_count }} registered</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('events.show', $nextEvent) }}" class="btn btn-sm btn-primary">Details</a>
                                        @if($nextEvent->is_registered)
                                            <button type="button" class="btn btn-sm btn-success" disabled>Registered</button>
                                        @else
                                            <form method="POST" action="{{ route('events.register', $nextEvent) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-light">Register</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="dashboard-next-event-card border rounded p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-1">No upcoming events</h6>
                                    <p class="text-muted fs-13 mb-0">We’ll add events here once they’re announced.</p>
                                </div>
                                <a href="{{ route('events.index') }}" class="btn btn-sm btn-light">Browse Events</a>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card stretch">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('events.index') }}" class="btn btn-light w-100 text-start">
                            <i class="feather-calendar me-2"></i>Explore Events
                        </a>
                        <a href="{{ route('programs.index') }}" class="btn btn-light w-100 text-start">
                            <i class="feather-grid me-2"></i>View Programs
                        </a>
                        <a href="{{ route('invoices.index') }}" class="btn btn-light w-100 text-start">
                            <i class="feather-file-text me-2"></i>Invoices
                        </a>
                        <a href="{{ route('settings.index') }}" class="btn btn-light w-100 text-start">
                            <i class="feather-settings me-2"></i>Settings
                        </a>
                        <a href="{{ route('notifications.index') }}" class="btn btn-light w-100 text-start">
                            <i class="feather-bell me-2"></i>Notifications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-md-6 col-xl-4">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12">Upcoming Events</span>
                            <h4 class="mb-0">{{ $stats['upcoming_events'] }}</h4>
                        </div>
                        <div class="avatar-text bg-soft-primary text-primary">
                            <i class="feather-calendar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12">Registered</span>
                            <h4 class="mb-0">{{ $stats['registered_events'] }}</h4>
                        </div>
                        <div class="avatar-text bg-soft-success text-success">
                            <i class="feather-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12">Open Invoices</span>
                            <h4 class="mb-0">{{ $stats['open_invoices'] }}</h4>
                        </div>
                        <div class="avatar-text bg-soft-warning text-warning">
                            <i class="feather-file-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-xl-7">
            <div class="card stretch">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Upcoming Events</h5>
                    <a href="{{ route('events.index') }}" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body">
                    @forelse($upcomingEvents as $event)
                        @php
                            $eventStatus = $event->status?->value ?? 'upcoming';
                            $statusBadge = match ($eventStatus) {
                                'ongoing' => 'bg-soft-info text-info',
                                'cancelled' => 'bg-soft-danger text-danger',
                                default => 'bg-soft-success text-success',
                            };
                        @endphp
                        <div class="d-flex flex-column flex-md-row align-items-start justify-content-between gap-3 border-bottom pb-3 mb-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="mb-0">{{ $event->title }}</h6>
                                    <span class="badge {{ $statusBadge }}">{{ ucfirst($eventStatus) }}</span>
                                    @if($event->is_registered)
                                        <span class="badge bg-soft-success text-success">Registered</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-3 text-muted fs-13">
                                    <span><i class="feather-calendar me-1"></i>{{ $event->starts_at?->format('d M Y, H:i') ?? 'Date TBA' }}</span>
                                    <span><i class="feather-map-pin me-1"></i>{{ $event->location ?? 'Location TBA' }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-light">Details</a>
                                @if($event->is_registered)
                                    <button type="button" class="btn btn-sm btn-success" disabled>Registered</button>
                                @else
                                    <form method="POST" action="{{ route('events.register', $event) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Register</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto mb-3">
                                <i class="feather-calendar"></i>
                            </div>
                            <h6 class="mb-2">No upcoming events</h6>
                            <p class="text-muted mb-0">Events will appear here when announced.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivity as $activity)
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="avatar-text bg-soft-primary text-primary">
                                <i class="feather-activity"></i>
                            </div>
                            <div>
                                <p class="mb-1 fw-semibold">{{ $activity->action }}</p>
                                <p class="text-muted fs-13 mb-0">{{ $activity->description ?? 'Activity recorded.' }}</p>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <div class="avatar-text avatar-xl bg-soft-secondary text-muted mx-auto mb-3">
                                <i class="feather-clock"></i>
                            </div>
                            <h6 class="mb-2">No recent activity</h6>
                            <p class="text-muted mb-0">Actions will appear here as you use the dashboard.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Recent Invoices</h5>
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body">
                    @forelse($recentInvoices as $invoice)
                        @php
                            $invoiceStatus = $invoice->status?->value ?? 'pending';
                            $invoiceBadge = match ($invoiceStatus) {
                                'paid' => 'bg-soft-success text-success',
                                'overdue' => 'bg-soft-danger text-danger',
                                'cancelled' => 'bg-soft-secondary text-muted',
                                default => 'bg-soft-warning text-warning',
                            };
                        @endphp
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <p class="mb-1 fw-semibold">#{{ $invoice->invoice_number }}</p>
                                <p class="text-muted fs-13 mb-0">{{ $invoice->subscription?->subscriptionPlan?->name ?? 'Subscription' }}</p>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $invoiceBadge }}">{{ ucfirst($invoiceStatus) }}</span>
                                <p class="mb-0 fw-semibold">Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto mb-3">
                                <i class="feather-file-text"></i>
                            </div>
                            <h6 class="mb-2">No invoices yet</h6>
                            <p class="text-muted mb-0">Invoices will appear after payments are processed.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card stretch">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Latest Notifications</h5>
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light">View All</a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($recentNotifications as $notification)
                            <div class="col-md-6 col-xl-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="avatar-text bg-soft-info text-info">
                                            <i class="feather-bell"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $notification->title ?? 'Notification' }}</h6>
                                            <p class="text-muted fs-13 mb-2">
                                                {{ $notification->message ?? $notification->data['message'] ?? 'New update available.' }}
                                            </p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-4">
                                    <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto mb-3">
                                        <i class="feather-bell"></i>
                                    </div>
                                    <h6 class="mb-2">No notifications yet</h6>
                                    <p class="text-muted mb-0">You will see updates and reminders here.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
