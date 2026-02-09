<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }
        .brand {
            font-size: 18px;
            font-weight: bold;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h4 {
            margin: 0 0 6px;
            font-size: 13px;
            text-transform: uppercase;
            color: #6b7280;
        }
        .summary {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            background: #e0f2fe;
            color: #0369a1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th,
        table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        table th {
            background: #f8fafc;
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
        }
    </style>
</head>
<body>
    @php
        $statusValue = $invoice->status?->value ?? 'pending';
        $statusLabel = ucfirst($statusValue);
    @endphp

    <div class="header">
        <div>
            <div class="brand">{{ config('app.name') }}</div>
            <div>Invoice #{{ $invoice->invoice_number }}</div>
            <div>Issued {{ $invoice->created_at->format('d M Y') }}</div>
        </div>
        <div class="badge">{{ $statusLabel }}</div>
    </div>

    <div class="section">
        <h4>Billed To</h4>
        <div>{{ $invoice->user->name }}</div>
        <div>{{ $invoice->user->email }}</div>
        <div>{{ $invoice->user->profile?->phone ?? 'Phone not provided' }}</div>
    </div>

    <div class="section">
        <h4>Plan Details</h4>
        <div>{{ $invoice->subscription?->subscriptionPlan?->name ?? 'Subscription Plan' }}</div>
        <div>
            {{ $invoice->subscription?->starts_at?->format('d M Y') ?? 'Start date TBA' }}
            -
            {{ $invoice->subscription?->ends_at?->format('d M Y') ?? 'End date TBA' }}
        </div>
        <div>Due {{ $invoice->due_date->format('d M Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Subscription: {{ $invoice->subscription?->subscriptionPlan?->name ?? 'Membership' }}</td>
                <td>Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="summary">
        <span>Total</span>
        <span>Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}</span>
    </div>
</body>
</html>
