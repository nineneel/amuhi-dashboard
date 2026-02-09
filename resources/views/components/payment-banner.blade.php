@props(['user'])

@php
    use App\SubscriptionStatus;

    $subscription = $user->currentSubscription();
    $hasActiveSubscription = $user->hasActiveSubscription();

    if ($hasActiveSubscription) {
        return;
    }

    $status = $subscription?->status;

    $alertClass = match($status?->value) {
        'pending' => 'alert-info',
        'expired' => 'alert-danger',
        default => 'alert-warning',
    };

    $icon = match($status?->value) {
        'pending' => 'feather-clock',
        'expired' => 'feather-alert-circle',
        default => 'feather-credit-card',
    };

    $message = match($status?->value) {
        'pending' => 'Your payment is being processed. You will get access once the payment is confirmed.',
        'expired' => 'Your subscription has expired. Renew now to continue accessing premium features.',
        default => 'Upgrade to a paid plan to access all features including events, programs, and invoices.',
    };

    $buttonText = match($status?->value) {
        'pending' => 'View Payment Status',
        'expired' => 'Renew Subscription',
        default => 'Upgrade Now',
    };
@endphp

@if(!$hasActiveSubscription)
<div class="alert {{ $alertClass }} alert-dismissible fade show mb-0 rounded-0" role="alert">
    <div class="d-flex align-items-center gap-3">
        <i class="{{ $icon }} fs-3"></i>
        <div class="flex-grow-1">
            <strong>
                @switch($status?->value)
                    @case('pending')
                        Payment Pending
                        @break
                    @case('expired')
                        Subscription Expired
                        @break
                    @default
                        Free Plan
                @endswitch
            </strong>
            <p class="mb-0 fs-13">{{ $message }}</p>
        </div>
        <a href="#" class="btn btn-sm {{ $status?->value === 'expired' ? 'btn-danger' : 'btn-primary' }}">
            {{ $buttonText }}
        </a>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
