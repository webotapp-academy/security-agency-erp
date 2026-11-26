<div class="space-y-3.5">

    <!-- METRICS CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 no-print">
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Total Invoiced</span>
            <div class="mt-1 text-base font-bold text-slate-900">₹{{ number_format($totalBilled) }}</div>
            <span class="text-[10px] text-slate-400">Cumulative billings</span>
        </div>
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Collections Received</span>
            <div class="mt-1 text-base font-bold text-emerald-700">₹{{ number_format($totalReceived) }}</div>
            <span class="text-[10px] text-emerald-600 font-semibold">Cleared into bank</span>
        </div>
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Outstanding Dues</span>
            <div class="mt-1 text-base font-bold text-rose-700">₹{{ number_format($totalOutstanding) }}</div>
            <span class="text-[10px] text-slate-400">Client receivable</span>
        </div>
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Overdue Invoices</span>
            <div class="mt-1 text-base font-bold text-rose-600">{{ $overdueCount }} <span class="text-xs font-normal text-slate-500">Invoices</span></div>
            <span class="text-[10px] text-rose-600 font-semibold">Requires follow-up</span>
        </div>
    </div>

    <!-- CONTROLS & FILTER BAR -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5 no-print">
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative w-56">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search invoice #, client..."
                       class="w-full pl-8 pr-2.5 py-1 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Client Filter -->
            <select wire:model.live="clientFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="">All Clients</option>
                @foreach ($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->client_name }}</option>
                @endforeach
            </select>

            <!-- Status Filter -->
            <select wire:model.live="statusFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="">All Statuses</option>
                <option value="sent">Sent (Unpaid)</option>
                <option value="partially_paid">Partially Paid</option>
                <option value="paid">Fully Paid</option>
                <option value="overdue">Overdue</option>
            </select>
        </div>

        <button wire:click="openCreateInvoiceModal" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white flex items-center space-x-1.5 shadow-xs cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Generate Client Invoice</span>
        </button>
    </div>

    <!-- INVOICES TABLE -->
    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th>Invoice #</th>
                        <th>Client & Site</th>
                        <th>Billing Month</th>
                        <th>Manned Guards</th>
                        <th>Subtotal</th>
                        <th>GST (18%)</th>
                        <th>Total Amount</th>
                        <th>Balance Due</th>
                        <th>Status</th>
                        <th class="text-right no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono text-xs">
                    @forelse ($invoices as $inv)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="font-bold text-slate-900">{{ $inv->invoice_number }}</td>
                            <td class="font-sans">
                                <div class="font-bold text-slate-900 text-xs">{{ $inv->client->client_name ?? '—' }}</div>
                                <span class="text-[10px] text-slate-400">{{ $inv->site->site_name ?? 'All Client Sites' }}</span>
                            </td>
                            <td class="font-sans text-slate-700">{{ $inv->billing_period }}</td>
                            <td class="text-slate-800">{{ $inv->guard_count }} Guards</td>
                            <td>₹{{ number_format($inv->subtotal) }}</td>
                            <td class="text-slate-500">₹{{ number_format($inv->cgst_amount + $inv->sgst_amount) }}</td>
                            <td class="font-bold text-slate-900">₹{{ number_format($inv->total_amount) }}</td>
                            <td>
                                @if ($inv->balance_amount > 0)
                                    <span class="font-bold text-rose-700">₹{{ number_format($inv->balance_amount) }}</span>
                                @else
                                    <span class="text-emerald-700 font-semibold">₹0 (Paid)</span>
                                @endif
                            </td>
                            <td class="font-sans">
                                @php
                                    $stClass = [
                                        'draft' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        'sent' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'partially_paid' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'overdue' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border capitalize {{ $stClass[$inv->status] ?? '' }}">
                                    {{ str_replace('_', ' ', $inv->status) }}
                                </span>
                            </td>
                            <td class="text-right whitespace-nowrap font-sans no-print">
                                <button wire:click="viewInvoicePrint({{ $inv->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 mr-1 cursor-pointer">
                                    Tax Invoice
                                </button>
                                @if ($inv->status !== 'paid')
                                    <button wire:click="openPaymentModal({{ $inv->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer">
                                        + Payment
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-6 text-xs text-slate-500 font-sans">
                                No client invoices generated yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-2 border-t border-slate-200 bg-slate-50/50">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- CREATE INVOICE MODAL -->
    @if ($showInvoiceModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-lg w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Generate Monthly Client Invoice</h3>
                    <button wire:click="$set('showInvoiceModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="createInvoice" class="p-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Select Client *</label>
                        <select wire:model.live="client_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="">-- Choose Client --</option>
                            @foreach ($clients as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->client_name }} ({{ $cl->client_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Specific Site (Optional)</label>
                        <select wire:model="site_id" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="">Consolidated (All Client Sites)</option>
                            @foreach ($sites as $st)
                                @if (!$client_id || $st->client_id == $client_id)
                                    <option value="{{ $st->id }}">{{ $st->site_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Billing Period *</label>
                            <input type="text" wire:model="billing_period" placeholder="e.g. November 2026" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Number of Guards *</label>
                            <input type="number" min="1" wire:model="guard_count" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Rate Per Guard (₹/month) *</label>
                            <input type="number" step="100" wire:model="rate_per_guard" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">GST Rate (%)</label>
                            <input type="number" step="0.5" wire:model="gst_rate" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Additional Charges (₹)</label>
                            <input type="number" step="100" wire:model="additional_charges" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Additional Charges Reason</label>
                            <input type="text" wire:model="additional_charges_desc" placeholder="Scanner / Gunman / Fuel" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Invoice Issue Date *</label>
                            <input type="date" wire:model="issue_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Payment Due Date *</label>
                            <input type="date" wire:model="due_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showInvoiceModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            Generate Tax Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- RECORD PAYMENT MODAL -->
    @if ($showPaymentModal && $selectedInvoice)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-sm w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Record Payment Receipt</h3>
                    <button wire:click="$set('showPaymentModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="recordInvoicePayment" class="p-4 space-y-3 text-xs">
                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded">
                        <span class="text-slate-500 block text-[10px]">Invoice Balance Due:</span>
                        <span class="font-bold text-base text-rose-700 font-mono">₹{{ number_format($selectedInvoice->balance_amount) }}</span>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Payment Received Amount (₹) *</label>
                        <input type="number" step="0.01" wire:model="payment_amount" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono font-bold">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Payment Date *</label>
                        <input type="date" wire:model="payment_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Payment Mode *</label>
                        <select wire:model="payment_mode" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="bank_transfer">Bank Transfer / NEFT / RTGS</option>
                            <option value="cheque">Cheque</option>
                            <option value="upi">UPI / Online</option>
                            <option value="cash">Cash Receipt</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Transaction Ref / Cheque No.</label>
                        <input type="text" wire:model="transaction_reference" placeholder="UTR or Cheque Number" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showPaymentModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-emerald-700 hover:bg-emerald-800 text-white font-semibold">
                            Record Receipt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- PRINTABLE TAX INVOICE VIEW MODAL -->
    @if ($showPrintModal && $selectedInvoice)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-3xl w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50 no-print">
                    <span class="font-bold text-xs text-slate-900 uppercase">Tax Invoice: {{ $selectedInvoice->invoice_number }}</span>
                    <div class="flex items-center space-x-2">
                        <button onclick="window.print()" class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-900 text-white flex items-center space-x-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Print Tax Invoice</span>
                        </button>
                        <button wire:click="$set('showPrintModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                    </div>
                </div>

                <!-- Printable Tax Invoice Body -->
                <div class="p-8 text-xs text-slate-800 space-y-4 print-break-inside-avoid">
                    <!-- Invoice Header -->
                    <div class="flex justify-between items-start border-b border-slate-300 pb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 uppercase tracking-tight">Garuda Apex Security Services Pvt. Ltd.</h2>
                            <p class="text-[11px] text-slate-600">Plot 42, G.S. Road, Christian Basti, Guwahati, Assam - 781005</p>
                            <p class="text-[11px] text-slate-600">GSTIN: <span class="font-mono font-bold">18AABCG1234F1Z8</span> &bull; State: Assam (18)</p>
                            <p class="text-[11px] text-slate-500">Email: accounts@garudasecurity.in &bull; Ph: +91 98640 12345</p>
                        </div>
                        <div class="text-right">
                            <h3 class="text-lg font-bold text-slate-900 uppercase tracking-wider">TAX INVOICE</h3>
                            <div class="font-mono text-xs font-bold text-slate-800 mt-1">{{ $selectedInvoice->invoice_number }}</div>
                            <div class="text-[11px] text-slate-500">Date: {{ $selectedInvoice->issue_date->format('d M Y') }}</div>
                            <div class="text-[11px] text-slate-500">Due: {{ $selectedInvoice->due_date->format('d M Y') }}</div>
                        </div>
                    </div>

                    <!-- Client Billed To -->
                    <div class="grid grid-cols-2 gap-4 p-3 bg-slate-50 rounded border border-slate-200 text-[11px]">
                        <div>
                            <span class="text-slate-500 uppercase font-bold text-[10px] block mb-1">Billed To (Client):</span>
                            <div class="font-bold text-slate-900 text-xs">{{ $selectedInvoice->client->client_name }}</div>
                            <div class="text-slate-700">{{ $selectedInvoice->client->company_name }}</div>
                            <div class="text-slate-600">{{ $selectedInvoice->client->billing_address }}</div>
                            <div class="text-slate-700 mt-0.5">GSTIN: <span class="font-mono font-semibold">{{ $selectedInvoice->client->gstin ?? 'N/A' }}</span></div>
                            <div class="text-slate-600">State: {{ $selectedInvoice->client->state }}</div>
                        </div>
                        <div>
                            <span class="text-slate-500 uppercase font-bold text-[10px] block mb-1">Deployment Details:</span>
                            <div><span class="text-slate-500">Deployment Site:</span> <span class="font-semibold">{{ $selectedInvoice->site->site_name ?? 'Consolidated Sites' }}</span></div>
                            <div><span class="text-slate-500">Billing Period:</span> <span class="font-semibold">{{ $selectedInvoice->billing_period }}</span></div>
                            <div><span class="text-slate-500">Guards Deployed:</span> <span class="font-semibold">{{ $selectedInvoice->guard_count }} Security Guards</span></div>
                            <div><span class="text-slate-500">Payment Status:</span> <span class="font-bold uppercase text-emerald-700">{{ $selectedInvoice->status }}</span></div>
                        </div>
                    </div>

                    <!-- Line Items Table -->
                    <div class="border border-slate-300 rounded overflow-hidden">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-100 border-b border-slate-300 font-bold text-slate-700">
                                    <th class="p-2 w-10">#</th>
                                    <th class="p-2">Description of Services (SAC: 998529)</th>
                                    <th class="p-2 text-center">Qty (Guards)</th>
                                    <th class="p-2 text-right">Unit Rate (₹)</th>
                                    <th class="p-2 text-right">Taxable Amount (₹)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr>
                                    <td class="p-2 font-mono">1</td>
                                    <td class="p-2">
                                        <div class="font-semibold text-slate-900">Manned Security Guard Services</div>
                                        <div class="text-[10px] text-slate-500">For the billing cycle: {{ $selectedInvoice->billing_period }}</div>
                                    </td>
                                    <td class="p-2 text-center font-mono">{{ $selectedInvoice->guard_count }}</td>
                                    <td class="p-2 text-right font-mono">₹{{ number_format($selectedInvoice->rate_per_guard, 2) }}</td>
                                    <td class="p-2 text-right font-mono font-semibold">₹{{ number_format($selectedInvoice->guard_count * $selectedInvoice->rate_per_guard, 2) }}</td>
                                </tr>
                                @if ($selectedInvoice->additional_charges > 0)
                                    <tr>
                                        <td class="p-2 font-mono">2</td>
                                        <td class="p-2">
                                            <div class="font-semibold text-slate-900">Additional Operational Charges</div>
                                            <div class="text-[10px] text-slate-500">{{ $selectedInvoice->additional_charges_desc ?? 'Special equipment and patrol' }}</div>
                                        </td>
                                        <td class="p-2 text-center font-mono">1</td>
                                        <td class="p-2 text-right font-mono">₹{{ number_format($selectedInvoice->additional_charges, 2) }}</td>
                                        <td class="p-2 text-right font-mono font-semibold">₹{{ number_format($selectedInvoice->additional_charges, 2) }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Tax Calculation & Totals -->
                    <div class="flex justify-end">
                        <div class="w-72 space-y-1.5 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal (Taxable Value):</span>
                                <span class="font-mono font-semibold text-slate-900">₹{{ number_format($selectedInvoice->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>CGST (9%):</span>
                                <span class="font-mono">₹{{ number_format($selectedInvoice->cgst_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>SGST (9%):</span>
                                <span class="font-mono">₹{{ number_format($selectedInvoice->sgst_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-bold border-t border-slate-300 pt-1.5 text-slate-900">
                                <span>Total Invoice Value:</span>
                                <span class="font-mono">₹{{ number_format($selectedInvoice->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs font-semibold text-emerald-700">
                                <span>Amount Received:</span>
                                <span class="font-mono">₹{{ number_format($selectedInvoice->paid_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs font-bold text-rose-700 border-t border-slate-200 pt-1">
                                <span>Balance Payable:</span>
                                <span class="font-mono">₹{{ number_format($selectedInvoice->balance_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details & Terms -->
                    <div class="p-3 bg-slate-50 rounded border border-slate-200 text-[11px] text-slate-600 space-y-1">
                        <div class="font-bold text-slate-800">Bank Payment Details:</div>
                        <div>Account Name: <span class="font-semibold text-slate-900">Garuda Apex Security Services Pvt. Ltd.</span></div>
                        <div>Bank: <span class="font-semibold text-slate-900">State Bank of India &bull; A/C: 309988776655 &bull; IFSC: SBIN0001234</span></div>
                    </div>

                    <!-- Footer & Signature -->
                    <div class="pt-6 flex justify-between text-center text-xs">
                        <div class="text-left text-[11px] text-slate-500 max-w-sm">
                            <p class="font-semibold text-slate-700">Terms & Conditions:</p>
                            <p>1. Payment is due within {{ $selectedInvoice->client->payment_terms ?? '30 days' }} of invoice receipt.</p>
                            <p>2. Subject to Guwahati jurisdiction.</p>
                        </div>
                        <div>
                            <p class="text-[11px] text-slate-600 font-medium">For Garuda Apex Security Services Pvt. Ltd.</p>
                            <div class="w-40 border-t border-slate-400 mx-auto mt-8"></div>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Authorized Signatory</span>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-2.5 border-t border-slate-200 bg-slate-50 flex justify-end no-print">
                    <button wire:click="$set('showPrintModal', false)" class="px-3 py-1 bg-slate-200 hover:bg-slate-300 rounded text-xs font-medium text-slate-700">Close</button>
                </div>
            </div>
        </div>
    @endif

</div>
