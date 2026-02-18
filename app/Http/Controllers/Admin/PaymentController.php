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
        $status = (string) $request->query('status', '');
        $perPage = (int) $request->query('per_page', 15);

        $payments = Payment::query()
            ->with(['invoice.user', 'invoice.subscription.subscriptionPlan'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('gateway_transaction_id', 'like', "%{$search}%")
                        ->orWhereHas('invoice.user', function ($uq) use ($search): void {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
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
