@extends('layouts.app')

@section('title', 'Invoices')
@section('header', 'Invoices')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item">Invoices</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12">Total Invoices</span>
                            <h4 class="mb-0">{{ $summary['total'] }}</h4>
                        </div>
                        <div class="avatar-text bg-soft-primary text-primary">
                            <i class="feather-file-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12">Paid</span>
                            <h4 class="mb-0">{{ $summary['paid'] }}</h4>
                        </div>
                        <div class="avatar-text bg-soft-success text-success">
                            <i class="feather-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12">Pending</span>
                            <h4 class="mb-0">{{ $summary['pending'] }}</h4>
                        </div>
                        <div class="avatar-text bg-soft-warning text-warning">
                            <i class="feather-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12">Overdue</span>
                            <h4 class="mb-0">{{ $summary['overdue'] }}</h4>
                        </div>
                        <div class="avatar-text bg-soft-danger text-danger">
                            <i class="feather-alert-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card stretch stretch-full mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Invoice History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
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
                            <tr>
                                <td class="fw-semibold">#{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->subscription?->subscriptionPlan?->name ?? 'Subscription' }}</td>
                                <td>Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}</td>
                                <td>{{ $invoice->due_date->format('d M Y') }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-light">
                                        View
                                    </a>
                                    <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-sm btn-primary">
                                        PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avatar-text avatar-xl bg-soft-primary text-primary mb-3">
                                            <i class="feather-file"></i>
                                        </div>
                                        <h6 class="mb-2">No invoices yet</h6>
                                        <p class="text-muted mb-0">Invoices will appear here once payments are processed.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
