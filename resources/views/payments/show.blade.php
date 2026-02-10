@extends('layouts.app')

@section('title', 'Subscription')
@section('header', 'Subscription')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item">Subscription</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-12">
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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>We couldn't process that request.</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="alert alert-info d-flex align-items-center gap-3 mb-0" role="alert">
                <i class="feather-info fs-4"></i>
                <div>
                    <strong>Demo mode is enabled.</strong>
                    <span class="d-block fs-13">Payments are simulated and no real charges are made.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-xl-8">
            <div class="card stretch stretch-full">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Choose Your Plan</h5>
                        <p class="text-muted mb-0 fs-13">Select a subscription plan to unlock all AMUHI features.</p>
                    </div>
                    <span class="badge bg-soft-primary text-primary">Demo Mode</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @forelse($plans as $plan)
                            @php
                                $isSelected = $selectedPlanId === $plan->id;
                                $isCurrent = $currentSubscription?->subscription_plan_id === $plan->id;
                            @endphp
                            <div class="col-md-6">
                                <div class="card h-100 border {{ $isSelected ? 'border-primary' : 'border-light' }}">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex align-items-start justify-content-between mb-3">
                                            <div>
                                                <h6 class="mb-1">{{ $plan->name }}</h6>
                                                <p class="text-muted fs-12 mb-0">{{ $plan->description }}</p>
                                            </div>
                                            @if($isCurrent)
                                                <span class="badge bg-soft-success text-success">Current</span>
                                            @else
                                                <span class="badge bg-soft-info text-info">Available</span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-baseline gap-2 mb-3">
                                            <span class="fs-3 fw-bold text-dark">Rp {{ number_format((float) $plan->price, 0, ',', '.') }}</span>
                                            <span class="text-muted fs-12">/ {{ $plan->duration_days ? $plan->duration_days.' days' : 'Lifetime' }}</span>
                                        </div>
                                        @if(! empty($plan->features))
                                            <ul class="list-unstyled mb-4">
                                                @foreach($plan->features as $feature)
                                                    <li class="d-flex align-items-center gap-2 mb-2">
                                                        <i class="feather-check text-success"></i>
                                                        <span class="fs-13">{{ $feature }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted fs-13 mb-4">Plan details will be available soon.</p>
                                        @endif
                                        <form method="POST" action="{{ route('payments.simulate') }}" class="mt-auto">
                                            @csrf
                                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="feather-credit-card me-1"></i>Pay Now (Demo)
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning mb-0">
                                    No active subscription plans are available yet.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            @php
                $statusValue = $currentSubscription?->status?->value ?? 'unpaid';
                $statusLabel = match ($statusValue) {
                    'active' => 'Active',
                    'pending' => 'Pending',
                    'expired' => 'Expired',
                    'unpaid' => 'Unpaid',
                    default => 'Unpaid',
                };
                $statusClass = match ($statusValue) {
                    'active' => 'bg-soft-success text-success',
                    'pending' => 'bg-soft-info text-info',
                    'expired' => 'bg-soft-danger text-danger',
                    default => 'bg-soft-warning text-warning',
                };
                $statusMessage = match ($statusValue) {
                    'active' => 'Your subscription is active. Enjoy full access to all AMUHI features.',
                    'pending' => 'Your payment is being processed. Access will unlock once confirmed.',
                    'expired' => 'Your subscription has expired. Renew to regain full access.',
                    default => 'You are currently on the free plan. Upgrade to unlock premium features.',
                };
            @endphp
            <div class="card stretch">
                <div class="card-header">
                    <h5 class="card-title mb-0">Current Subscription</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0">Status</h6>
                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                    <p class="text-muted fs-13">{{ $statusMessage }}</p>
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted fs-13">Plan</span>
                            <span class="fw-semibold">{{ $currentSubscription?->subscriptionPlan?->name ?? 'No plan selected' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <span class="text-muted fs-13">Valid Until</span>
                            <span class="fw-semibold">
                                @if($currentSubscription?->ends_at)
                                    {{ $currentSubscription->ends_at->format('d M Y') }}
                                @elseif($statusValue === 'active')
                                    Lifetime
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card stretch mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Demo Controls</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted fs-13">Use this section to manually toggle subscription status for testing.</p>
                    <form method="POST" action="{{ route('payments.status') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="plan_id" class="form-label">Plan</label>
                            <select id="plan_id" name="plan_id" class="form-select @error('plan_id') is-invalid @enderror">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('plan_id', $selectedPlanId) == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} (Rp {{ number_format((float) $plan->price, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('plan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                                @foreach(\App\SubscriptionStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ old('status', $statusValue) === $status->value ? 'selected' : '' }}>
                                        {{ ucfirst($status->value) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-light w-100">
                            <i class="feather-refresh-ccw me-1"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
