<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Client;
use App\Models\Site;
use App\Models\Setting;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ClientBillingManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $clientFilter = '';

    // Modal state
    public $showInvoiceModal = false;
    public $showPaymentModal = false;
    public $showPrintModal = false;

    public $selectedInvoice = null;

    // Create Invoice fields
    public $client_id = '';
    public $site_id = '';
    public $billing_period = '';
    public $issue_date = '';
    public $due_date = '';
    public $guard_count = 1;
    public $rate_per_guard = 25000;
    public $additional_charges = 0;
    public $additional_charges_desc = '';
    public $discount_amount = 0;
    public $gst_rate = 18.00;
    public $notes = '';

    // Record Payment fields
    public $paymentInvoiceId = null;
    public $payment_amount = 0;
    public $payment_date = '';
    public $payment_mode = 'bank_transfer';
    public $transaction_reference = '';
    public $payment_notes = '';

    public function mount()
    {
        $this->issue_date = now()->toDateString();
        $this->due_date = now()->addDays(30)->toDateString();
        $this->billing_period = now()->format('F Y');
        $this->payment_date = now()->toDateString();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateInvoiceModal()
    {
        $this->resetValidation();
        $firstClient = Client::where('status', 'active')->first();
        $this->client_id = $firstClient ? $firstClient->id : '';
        $this->site_id = '';
        $this->billing_period = now()->format('F Y');
        $this->issue_date = now()->toDateString();
        $this->due_date = now()->addDays(30)->toDateString();
        $this->guard_count = 2;
        $this->rate_per_guard = 25000;
        $this->additional_charges = 0;
        $this->additional_charges_desc = '';
        $this->discount_amount = 0;
        $this->gst_rate = 18.00;
        $this->notes = 'Payment due within 30 days of invoice date.';

        $this->showInvoiceModal = true;
    }

    public function updatedClientId()
    {
        $site = Site::where('client_id', $this->client_id)->first();
        if ($site) {
            $this->site_id = $site->id;
            $this->guard_count = $site->required_guards;
        } else {
            $this->site_id = '';
        }
    }

    public function createInvoice()
    {
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'billing_period' => 'required|string',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'guard_count' => 'required|integer|min:1',
            'rate_per_guard' => 'required|numeric|min:0',
        ]);

        $subtotal = ($this->guard_count * $this->rate_per_guard) + (float)$this->additional_charges - (float)$this->discount_amount;
        $subtotal = max(0, $subtotal);

        // 18% GST (CGST 9% + SGST 9%)
        $gstAmount = ($subtotal * $this->gst_rate) / 100;
        $cgst = $gstAmount / 2;
        $sgst = $gstAmount / 2;
        $total = $subtotal + $gstAmount;

        $prefix = Setting::get('invoice_prefix', 'GSS-INV');
        $latest = Invoice::orderBy('id', 'desc')->first();
        $nextNum = $latest ? ($latest->id + 1) : 101;
        $invNumber = sprintf('%s-%s-%03d', $prefix, date('Y'), $nextNum);

        $invoice = Invoice::create([
            'invoice_number' => $invNumber,
            'client_id' => $this->client_id,
            'site_id' => $this->site_id ?: null,
            'billing_period' => $this->billing_period,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'guard_count' => $this->guard_count,
            'rate_per_guard' => $this->rate_per_guard,
            'subtotal' => $subtotal,
            'additional_charges' => $this->additional_charges ?: 0,
            'additional_charges_desc' => $this->additional_charges_desc,
            'discount_amount' => $this->discount_amount ?: 0,
            'gst_rate' => $this->gst_rate,
            'cgst_amount' => $cgst,
            'sgst_amount' => $sgst,
            'igst_amount' => 0.00,
            'total_amount' => $total,
            'paid_amount' => 0.00,
            'balance_amount' => $total,
            'status' => 'sent',
            'notes' => $this->notes,
        ]);

        AuditLog::log('invoice_created', 'invoices', $invoice->id, "Generated invoice {$invNumber} for client {$invoice->client->client_name} (₹{$total})");
        session()->flash('success', "Invoice {$invNumber} generated successfully.");

        $this->showInvoiceModal = false;
    }

    public function openPaymentModal($id)
    {
        $this->paymentInvoiceId = $id;
        $inv = Invoice::findOrFail($id);
        $this->payment_amount = $inv->balance_amount;
        $this->payment_date = now()->toDateString();
        $this->payment_mode = 'bank_transfer';
        $this->transaction_reference = '';
        $this->payment_notes = '';
        $this->showPaymentModal = true;
    }

    public function recordInvoicePayment()
    {
        $this->validate([
            'payment_amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_mode' => 'required',
        ]);

        $invoice = Invoice::findOrFail($this->paymentInvoiceId);

        $newPaid = (float)$invoice->paid_amount + (float)$this->payment_amount;
        $newBalance = max(0, (float)$invoice->total_amount - $newPaid);
        $newStatus = ($newBalance <= 0) ? 'paid' : 'partially_paid';

        $receiptNo = 'REC-' . date('Ymd') . '-' . rand(100, 999);

        InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'payment_receipt_number' => $receiptNo,
            'payment_date' => $this->payment_date,
            'amount' => $this->payment_amount,
            'payment_mode' => $this->payment_mode,
            'transaction_reference' => $this->transaction_reference,
            'notes' => $this->payment_notes,
            'recorded_by_id' => Auth::id(),
        ]);

        $invoice->update([
            'paid_amount' => $newPaid,
            'balance_amount' => $newBalance,
            'status' => $newStatus,
        ]);

        AuditLog::log('payment_received', 'invoices', $invoice->id, "Received payment ₹{$this->payment_amount} against invoice {$invoice->invoice_number} (Receipt: {$receiptNo})");
        session()->flash('success', "Payment receipt recorded. Invoice updated to " . strtoupper($newStatus));

        $this->showPaymentModal = false;
    }

    public function viewInvoicePrint($id)
    {
        $this->selectedInvoice = Invoice::with(['client', 'site', 'payments'])->findOrFail($id);
        $this->showPrintModal = true;
    }

    public function render()
    {
        $query = Invoice::with(['client', 'site', 'payments']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('client', function($cq) {
                      $cq->where('client_name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->clientFilter) {
            $query->where('client_id', $this->clientFilter);
        }

        $invoices = $query->orderBy('id', 'desc')->paginate(10);
        $clients = Client::where('status', 'active')->orderBy('client_name')->get();
        $sites = $this->client_id ? Site::where('client_id', $this->client_id)->get() : collect();

        $totalBilled = (float) Invoice::sum('total_amount');
        $totalCollected = (float) Invoice::sum('paid_amount');
        $totalOutstanding = (float) Invoice::sum('balance_amount');
        $overdueCount = Invoice::where('status', 'overdue')->count();

        return view('livewire.client-billing-manager', [
            'invoices' => $invoices,
            'clients' => $clients,
            'sites' => $sites,
            'totalBilled' => $totalBilled,
            'totalCollected' => $totalCollected,
            'totalReceived' => $totalCollected,
            'totalOutstanding' => $totalOutstanding,
            'overdueCount' => $overdueCount,
        ])->layout('layouts.app', ['title' => 'Client Billing & Invoices', 'subtitle' => 'GST Compliant Monthly Invoices & Receipts']);
    }
}
