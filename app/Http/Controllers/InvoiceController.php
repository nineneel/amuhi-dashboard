<?php

namespace App\Http\Controllers;

use App\InvoiceStatus;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $invoices = $user->invoices()
            ->with(['subscription.subscriptionPlan'])
            ->latest()
            ->get();

        $summary = [
            'total' => $invoices->count(),
            'paid' => $invoices->where('status', InvoiceStatus::Paid)->count(),
            'pending' => $invoices->where('status', InvoiceStatus::Pending)->count(),
            'overdue' => $invoices->where('status', InvoiceStatus::Overdue)->count(),
        ];

        return view('invoices.index', [
            'invoices' => $invoices,
            'summary' => $summary,
        ]);
    }

    public function show(Invoice $invoice, Request $request): View
    {
        $this->ensureInvoiceOwner($invoice, $request);

        $invoice->load(['subscription.subscriptionPlan', 'payments', 'user.profile']);

        return view('invoices.show', [
            'invoice' => $invoice,
        ]);
    }

    public function download(Invoice $invoice, Request $request): Response
    {
        $this->ensureInvoiceOwner($invoice, $request);

        $invoice->load(['subscription.subscriptionPlan', 'payments', 'user.profile']);

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
        ]);

        return $pdf->download('invoice-'.$invoice->invoice_number.'.pdf');
    }

    private function ensureInvoiceOwner(Invoice $invoice, Request $request): void
    {
        if ($invoice->user_id !== $request->user()->id) {
            abort(404);
        }
    }
}
