@extends('layouts.app')

@section('title', 'Notifications')
@section('header', 'Notifications')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item">Notifications</li>
@endsection

@section('content')
    <div class="d-flex justify-content-end mb-3">
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-light">
                <i class="feather-check me-2"></i>Mark All Read
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card stretch">
        <div class="card-body">
            <div class="d-flex flex-column gap-3">
                @forelse($notifications as $notification)
                    <div class="notification-item border rounded p-3 {{ $notification->read_at ? 'notification-item--read' : '' }}">
                        <div class="d-flex align-items-start gap-3">
                            <div class="avatar-text bg-soft-primary text-primary">
                                <i class="feather-bell"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <h6 class="mb-1">{{ $notification->title ?? 'Notification' }}</h6>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="text-muted fs-13 mb-2">
                                    {{ $notification->message ?? $notification->data['message'] ?? 'New update available.' }}
                                </p>
                                <div class="d-flex align-items-center gap-2">
                                    @if($notification->read_at)
                                        <span class="badge bg-soft-secondary text-muted">Read</span>
                                    @else
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-light">Mark as Read</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto mb-3">
                            <i class="feather-bell"></i>
                        </div>
                        <h6 class="mb-2">No notifications</h6>
                        <p class="text-muted mb-0">You're all caught up for now.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
