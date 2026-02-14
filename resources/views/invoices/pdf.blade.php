<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #111827;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .muted {
            color: #6b7280;
            font-size: 12px;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            font-size: 12px;
            text-align: left;
        }

        th {
            background: #f9fafb;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>
            <h1>Invoice {{ $invoice->invoice_number }}</h1>
            <p class="muted">Generated on {{ now()->format('d M Y, H:i') }}</p>
        </div>
        <div>
            <p class="muted">AMUHI Dashboard</p>
        </div>
    </div>

    <div class="card">
        <p><strong>Member:</strong> {{ $invoice->user->name }}</p>
        <p><strong>Email:</strong> {{ $invoice->user->email }}</p>
        <p><strong>Plan:</strong> {{ $invoice->subscription?->subscriptionPlan?->name ?? 'Membership' }}</p>
        <p><strong>Amount:</strong> Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($invoice->status?->value ?? 'pending') }}</p>
        <p><strong>Due Date:</strong> {{ optional($invoice->due_date)->format('d M Y') ?? '-' }}</p>
    </div>

    <div class="card">
        <h3>Payments</h3>
        @if ($invoice->payments->isEmpty())
            <p class="muted">No payments recorded.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Paid At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->gateway_transaction_id ?? '-' }}</td>
                            <td>Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($payment->status?->value ?? 'pending') }}</td>
                            <td>{{ optional($payment->paid_at)->format('d M Y, H:i') ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>

</html>
