<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $payments = Payment::query()
            ->with(['invoice.user'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('gateway_transaction_id', 'like', "%{$search}%")
                        ->orWhereHas('invoice', function ($invoiceQuery) use ($search): void {
                            $invoiceQuery
                                ->where('invoice_number', 'like', "%{$search}%")
                                ->orWhereHas('user', function ($userQuery) use ($search): void {
                                    $userQuery
                                        ->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
        ]);
    }

    public function show(Payment $payment): View
    {
        $payment->load(['invoice.user', 'invoice.subscription.subscriptionPlan']);

        return view('admin.payments.show', [
            'payment' => $payment,
        ]);
    }
}
