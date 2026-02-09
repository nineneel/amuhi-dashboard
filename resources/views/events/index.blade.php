@extends('layouts.app')

@section('title', 'Events')
@section('header', 'Events')

@section('page-header')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Events</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Events</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('events.calendar') }}" class="btn btn-light-brand">
                        <i class="feather-calendar me-2"></i>Calendar View
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
        <a href="{{ route('events.index') }}" class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-light' }}">
            All Events
        </a>
        <a href="{{ route('events.index', ['filter' => 'registered']) }}" class="btn btn-sm {{ $filter === 'registered' ? 'btn-primary' : 'btn-light' }}">
            Registered
        </a>
    </div>

    <div class="row g-4">
        @forelse($events as $event)
            @php
                $statusValue = $event->status?->value ?? 'upcoming';
                $statusLabel = match ($statusValue) {
                    'ongoing' => 'Ongoing',
                    'past' => 'Past',
                    'cancelled' => 'Cancelled',
                    default => 'Upcoming',
                };
                $statusClass = match ($statusValue) {
                    'ongoing' => 'bg-soft-info text-info',
                    'past' => 'bg-soft-secondary text-muted',
                    'cancelled' => 'bg-soft-danger text-danger',
                    default => 'bg-soft-success text-success',
                };
                $dateLabel = $event->starts_at ? $event->starts_at->format('d M Y, H:i') : 'Date TBA';
                $locationLabel = $event->location ?: 'Location TBA';
            @endphp
            <div class="col-12">
                <div class="card stretch">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h5 class="mb-0">{{ $event->title }}</h5>
                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                    @if($event->is_registered)
                                        <span class="badge bg-soft-success text-success">Registered</span>
                                    @endif
                                </div>
                                <p class="text-muted mb-3 fs-13">
                                    {{ $event->description ? \Illuminate\Support\Str::limit($event->description, 140) : 'Event details will be announced soon.' }}
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-2 text-muted fs-13">
                                        <i class="feather-calendar"></i>
                                        <span>{{ $dateLabel }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-muted fs-13">
                                        <i class="feather-map-pin"></i>
                                        <span>{{ $locationLabel }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-muted fs-13">
                                        <i class="feather-users"></i>
                                        <span>{{ $event->registrations_count }} registered</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <a href="{{ route('events.show', $event) }}" class="btn btn-light">Details</a>
                                @if($event->is_registered)
                                    <button type="button" class="btn btn-success" disabled>Registered</button>
                                @elseif(in_array($statusValue, ['past', 'cancelled'], true))
                                    <button type="button" class="btn btn-secondary" disabled>Registration closed</button>
                                @else
                                    <form method="POST" action="{{ route('events.register', $event) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            <i class="feather-check-circle me-1"></i>Register
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto mb-3">
                            <i class="feather-calendar"></i>
                        </div>
                        <h5 class="mb-2">No events available</h5>
                        <p class="text-muted mb-0">New events will be announced soon. Check back later.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
