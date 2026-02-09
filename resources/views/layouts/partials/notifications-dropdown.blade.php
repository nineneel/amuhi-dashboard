@php
    $unreadNotifications = auth()->user()->notifications()->whereNull('read_at')->latest()->take(5)->get();
    $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
@endphp

<div class="dropdown nxl-h-item">
    <a class="nxl-head-link me-3" data-bs-toggle="dropdown" href="#" role="button" data-bs-auto-close="outside">
        <i class="feather-bell"></i>
        @if($unreadCount > 0)
            <span class="badge bg-danger nxl-h-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
        <div class="d-flex justify-content-between align-items-center notifications-head">
            <h6 class="fw-bold text-dark mb-0">Notifications</h6>
            @if($unreadCount > 0)
                <a href="javascript:void(0);" class="fs-11 text-success text-end ms-auto" data-bs-toggle="tooltip" title="Mark all as read">
                    <i class="feather-check"></i>
                    <span>Mark as Read</span>
                </a>
            @endif
        </div>

        @forelse($unreadNotifications as $notification)
            <div class="notifications-item">
                <div class="avatar-text avatar-md rounded-circle bg-soft-primary text-primary me-3">
                    <i class="feather-bell"></i>
                </div>
                <div class="notifications-desc">
                    <a href="javascript:void(0);" class="font-body text-truncate-2-line">
                        {{ $notification->data['message'] ?? 'New notification' }}
                    </a>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="notifications-date text-muted border-bottom border-bottom-dashed">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                        <div class="d-flex align-items-center float-end gap-2">
                            <a href="javascript:void(0);" class="d-block wd-8 ht-8 rounded-circle bg-gray-300" data-bs-toggle="tooltip" title="Mark as Read"></a>
                            <a href="javascript:void(0);" class="text-danger" data-bs-toggle="tooltip" title="Remove">
                                <i class="feather-x fs-12"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="d-flex flex-column align-items-center justify-content-center p-4">
                <i class="feather-bell fs-1 text-muted mb-3"></i>
                <p class="text-muted mb-0">No new notifications</p>
            </div>
        @endforelse

        @if($unreadCount > 0)
            <div class="text-center notifications-footer">
                <a href="javascript:void(0);" class="fs-13 fw-semibold text-dark">View All Notifications</a>
            </div>
        @endif
    </div>
</div>
