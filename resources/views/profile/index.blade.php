@extends('layouts.app')

@section('title', 'Profile')
@section('header', 'Profile')

@section('content')
    <div class="row g-4">
        <div class="col-xxl-5 col-xl-6">
            {{-- Profile Card --}}
            <div class="card stretch h-100">
                <div class="card-body d-flex flex-column">
                    <div class="mb-4 text-center">
                        @if($user->profile?->photo)
                            <div class="avatar-image avatar-xxl mx-auto mb-3">
                                <img src="{{ Storage::url($user->profile->photo) }}" alt="{{ $user->name }}" class="img-fluid rounded-circle w-100 h-100 object-fit-cover">
                            </div>
                        @else
                            <div class="avatar-text avatar-xxl rounded-circle bg-primary text-white mx-auto mb-3">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-2">{{ $user->email }}</p>
                        @if($user->hasActiveSubscription())
                            <span class="badge bg-soft-success text-success">Active Member</span>
                        @else
                            <span class="badge bg-soft-warning text-warning">Free Plan</span>
                        @endif
                    </div>

                    @if($user->profile?->bio)
                        <p class="text-muted fs-13 text-center">{{ $user->profile->bio }}</p>
                    @endif

                    <div class="mt-auto">
                        <div class="row g-2">
                            <div class="col-12 col-sm-6">
                                <a href="{{ route('settings.index', ['section' => 'account']) }}" class="btn btn-light w-100">
                                    <i class="feather-settings me-2"></i>Account Settings
                                </a>
                            </div>
                            <div class="col-12 col-sm-6">
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100">
                                    <i class="feather-edit me-2"></i>Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-7 col-xl-6">
            {{-- Profile Details --}}
            <div class="card stretch h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Profile Details</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="d-flex flex-column gap-3">
                        <div class="row align-items-start">
                            <div class="col-sm-5 col-6 text-muted fw-semibold">Full Name:</div>
                            <div class="col-sm-7 col-6 fw-medium text-dark text-sm-end text-md-start">{{ $user->name }}</div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col-sm-5 col-6 text-muted fw-semibold">Email Address:</div>
                            <div class="col-sm-7 col-6 fw-medium text-dark text-sm-end text-md-start">{{ $user->email }}</div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col-sm-5 col-6 text-muted fw-semibold">Phone Number:</div>
                            <div class="col-sm-7 col-6 fw-medium text-dark text-sm-end text-md-start">{{ $user->profile?->phone ?? '-' }}</div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col-sm-5 col-6 text-muted fw-semibold">Member Type:</div>
                            <div class="col-sm-7 col-6 fw-medium text-dark text-sm-end text-md-start">
                                @switch($user->profile?->member_type?->value)
                                    @case('ppui_pihk')
                                        PPUI / PIHK Member
                                        @break
                                    @case('pt')
                                        Company (PT)
                                        @break
                                    @case('personal')
                                        Personal
                                        @break
                                    @default
                                        -
                                @endswitch
                            </div>
                        </div>
                        @if($user->profile?->company_name)
                            <div class="row align-items-start">
                                <div class="col-sm-5 col-6 text-muted fw-semibold">Company Name:</div>
                                <div class="col-sm-7 col-6 fw-medium text-dark text-sm-end text-md-start">{{ $user->profile->company_name }}</div>
                            </div>
                        @endif
                        @if($user->profile?->address)
                            <div class="row align-items-start">
                                <div class="col-sm-5 col-6 text-muted fw-semibold">Address:</div>
                                <div class="col-sm-7 col-6 fw-medium text-dark text-sm-end text-md-start">{{ $user->profile->address }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            {{-- Subscription Info --}}
            <div class="card stretch">
                <div class="card-header">
                    <h5 class="card-title mb-0">Subscription</h5>
                </div>
                <div class="card-body">
                    @php
                        $subscription = $user->currentSubscription();
                    @endphp

                    @if($subscription && $user->hasActiveSubscription())
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-soft-success text-success rounded">
                                <i class="feather-check"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $subscription->subscriptionPlan?->name ?? 'Active Plan' }}</h6>
                                <p class="text-muted mb-0 fs-13">
                                    @if($subscription->ends_at)
                                        Valid until {{ $subscription->ends_at->format('d M Y') }}
                                    @else
                                        Lifetime access
                                    @endif
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-soft-warning text-warning rounded">
                                <i class="feather-alert-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">No Active Subscription</h6>
                                <p class="text-muted mb-0 fs-13">Upgrade to access all features</p>
                            </div>
                            <a href="{{ route('payments.show') }}" class="btn btn-primary">
                                Upgrade Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
