<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.pdf.invoice_title', ['number' => $invoice->invoice_number]) }}</title>
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
            <h1>{{ __('ui.pdf.invoice_title', ['number' => $invoice->invoice_number]) }}</h1>
            <p class="muted">{{ __('ui.pdf.generated_on', ['date' => now()->format('d M Y, H:i')]) }}</p>
        </div>
        <div>
            <p class="muted">AMUHI Dashboard</p>
        </div>
    </div>

    <div class="card">
        @php
            $invoiceStatus = $invoice->status?->value ?? 'pending';
            $invoiceStatusKey = 'ui.invoices.status_labels.'.$invoiceStatus;
            $invoiceStatusLabel = trans()->has($invoiceStatusKey) ? __($invoiceStatusKey) : ucfirst($invoiceStatus);
        @endphp
        <p><strong>{{ __('ui.pdf.member') }}</strong> {{ $invoice->user->name }}</p>
        <p><strong>{{ __('ui.forms.email') }}:</strong> {{ $invoice->user->email }}</p>
        <p><strong>{{ __('ui.invoices.plan') }}:</strong> {{ $invoice->subscription?->subscriptionPlan?->name ?? 'Membership' }}</p>
        <p><strong>{{ __('ui.invoices.amount') }}:</strong> Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}</p>
        <p><strong>{{ __('ui.invoices.status') }}:</strong> {{ $invoiceStatusLabel }}</p>
        <p><strong>{{ __('ui.invoices.due_date') }}:</strong> {{ optional($invoice->due_date)->format('d M Y') ?? '-' }}</p>
    </div>

    <div class="card">
        <h3>{{ __('ui.pdf.payments') }}</h3>
        @if ($invoice->payments->isEmpty())
            <p class="muted">{{ __('ui.pdf.no_payments') }}</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>{{ __('ui.invoices.transaction_id') }}</th>
                        <th>{{ __('ui.invoices.amount') }}</th>
                        <th>{{ __('ui.invoices.status') }}</th>
                        <th>{{ __('ui.invoices.paid_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->payments as $payment)
                        @php
                            $paymentStatus = $payment->status?->value ?? 'pending';
                            $paymentStatusKey = 'ui.invoices.payment_status_labels.'.$paymentStatus;
                            $paymentStatusLabel = trans()->has($paymentStatusKey) ? __($paymentStatusKey) : ucfirst($paymentStatus);
                        @endphp
                        <tr>
                            <td>{{ $payment->gateway_transaction_id ?? '-' }}</td>
                            <td>Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                            <td>{{ $paymentStatusLabel }}</td>
                            <td>{{ optional($payment->paid_at)->format('d M Y, H:i') ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>

</html>
