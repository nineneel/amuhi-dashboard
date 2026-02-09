@extends('layouts.app')

@section('title', 'Invoice Details')
@section('header', 'Invoice Details')

@section('page-header')
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Invoice Details</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
                <li class="breadcrumb-item">Details</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-primary">
                        <i class="feather-download me-2"></i>Download PDF
                    </a>
                    <a href="{{ route('invoices.index') }}" class="btn btn-light">
                        <i class="feather-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @php
        $statusValue = $invoice->status?->value ?? 'pending';
        $statusLabel = match ($statusValue) {
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled',
            default => 'Pending',
        };
        $statusClass = match ($statusValue) {
            'paid' => 'bg-soft-success text-success',
            'overdue' => 'bg-soft-danger text-danger',
            'cancelled' => 'bg-soft-secondary text-muted',
            default => 'bg-soft-warning text-warning',
        };
    @endphp

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card stretch">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                        <div>
                            <h4 class="mb-1">Invoice #{{ $invoice->invoice_number }}</h4>
                            <span class="text-muted fs-13">Issued {{ $invoice->created_at->format('d M Y') }}</span>
                        </div>
                        <span class="badge {{ $statusClass }} fs-13">{{ $statusLabel }}</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-uppercase text-muted fs-12">Billed To</h6>
                                <p class="mb-1 fw-semibold">{{ $invoice->user->name }}</p>
                                <p class="mb-1 text-muted fs-13">{{ $invoice->user->email }}</p>
                                <p class="mb-0 text-muted fs-13">{{ $invoice->user->profile?->phone ?? 'Phone not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-uppercase text-muted fs-12">Plan Details</h6>
                                <p class="mb-1 fw-semibold">{{ $invoice->subscription?->subscriptionPlan?->name ?? 'Subscription Plan' }}</p>
                                <p class="mb-1 text-muted fs-13">
                                    {{ $invoice->subscription?->starts_at?->format('d M Y') ?? 'Start date TBA' }}
                                    -
                                    {{ $invoice->subscription?->ends_at?->format('d M Y') ?? 'End date TBA' }}
                                </p>
                                <p class="mb-0 text-muted fs-13">Due {{ $invoice->due_date->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fs-13 text-muted">Total Amount</span>
                            <span class="fs-4 fw-bold">Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card stretch">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payment Activity</h5>
                </div>
                <div class="card-body">
                    @if($invoice->payments->isEmpty())
                        <div class="text-center">
                            <div class="avatar-text avatar-lg bg-soft-primary text-primary mx-auto mb-3">
                                <i class="feather-credit-card"></i>
                            </div>
                            <p class="text-muted mb-0">No payments recorded yet.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($invoice->payments as $payment)
                                @php
                                    $paymentStatus = $payment->status?->value ?? 'pending';
                                    $paymentBadge = match ($paymentStatus) {
                                        'success' => 'bg-soft-success text-success',
                                        'failed' => 'bg-soft-danger text-danger',
                                        'refunded' => 'bg-soft-warning text-warning',
                                        default => 'bg-soft-info text-info',
                                    };
                                @endphp
                                <div class="border rounded p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-semibold">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</span>
                                        <span class="badge {{ $paymentBadge }}">{{ ucfirst($paymentStatus) }}</span>
                                    </div>
                                    <p class="text-muted fs-12 mb-0">
                                        {{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : 'Pending confirmation' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
