@extends('layouts.app')

@section('title', 'Event Details')
@section('header', 'Event Details')

@section('page-header')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Event Details</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
                <li class="breadcrumb-item">Details</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('events.index') }}" class="btn btn-light">
                        <i class="feather-arrow-left me-2"></i>Back to Events
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
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
    @endphp

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

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card stretch">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <h4 class="mb-0">{{ $event->title }}</h4>
                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                        @if($isRegistered)
                            <span class="badge bg-soft-success text-success">Registered</span>
                        @endif
                    </div>

                    <div class="d-flex flex-wrap gap-4 mb-4">
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="feather-calendar"></i>
                            <span>
                                @if($event->starts_at)
                                    {{ $event->starts_at->format('d M Y, H:i') }}
                                @else
                                    Date TBA
                                @endif
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="feather-map-pin"></i>
                            <span>{{ $event->location ?: 'Location TBA' }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="feather-users"></i>
                            <span>{{ $event->event_registrations_count }} registered</span>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <h6 class="text-uppercase text-muted fs-12 mb-2">About this event</h6>
                        <p class="text-muted mb-0">
                            {{ $event->description ?: 'Full event details will be announced soon. Keep an eye on this page for updates.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card stretch">
                <div class="card-header">
                    <h5 class="card-title mb-0">Your Registration</h5>
                </div>
                <div class="card-body">
                    @if($isRegistered)
                        <div class="alert alert-success mb-3">
                            You are registered for this event.
                        </div>
                        <button type="button" class="btn btn-success w-100" disabled>
                            <i class="feather-check-circle me-1"></i>Registered
                        </button>
                    @elseif(in_array($statusValue, ['past', 'cancelled'], true))
                        <div class="alert alert-warning mb-3">
                            Registration is closed for this event.
                        </div>
                        <button type="button" class="btn btn-secondary w-100" disabled>
                            <i class="feather-lock me-1"></i>Registration closed
                        </button>
                    @else
                        <p class="text-muted fs-13">Register now to secure your spot. It's a one-click registration.</p>
                        <form method="POST" action="{{ route('events.register', $event) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="feather-check-circle me-1"></i>Register for Event
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card stretch mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Need help?</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted fs-13 mb-3">Questions about this event? Reach out to the AMUHI team.</p>
                    <a href="mailto:support@amuhi.id" class="btn btn-light w-100">
                        <i class="feather-mail me-1"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
