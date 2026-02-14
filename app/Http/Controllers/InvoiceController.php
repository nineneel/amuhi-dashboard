<?php

namespace App\Http\Controllers;

use App\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Contracts\View\Factory as ViewFactory;
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
            'paid' => $invoices->filter(fn (Invoice $invoice) => $invoice->status?->value === InvoiceStatus::Paid->value)->count(),
            'pending' => $invoices->filter(fn (Invoice $invoice) => $invoice->status?->value === InvoiceStatus::Pending->value)->count(),
            'overdue' => $invoices->filter(fn (Invoice $invoice) => $invoice->status?->value === InvoiceStatus::Overdue->value)->count(),
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

    public function download(Invoice $invoice, Request $request, ViewFactory $viewFactory): Response
    {
        $this->ensureInvoiceOwner($invoice, $request);

        $invoice->load(['subscription.subscriptionPlan', 'payments', 'user.profile']);
        $html = $viewFactory->make('invoices.pdf', ['invoice' => $invoice])->render();
        $filename = 'invoice-'.$invoice->invoice_number.'.html';

        return response()->streamDownload(
            function () use ($html): void {
                echo $html;
            },
            $filename,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    private function ensureInvoiceOwner(Invoice $invoice, Request $request): void
    {
        if ($invoice->user_id !== $request->user()->id) {
            abort(404);
        }
    }
}
