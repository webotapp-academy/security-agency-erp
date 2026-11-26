<div class="space-y-3.5">

    <!-- FINANCIAL SUMMARY CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 no-print">
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Gross Payroll</span>
            <div class="mt-1 text-base font-bold text-slate-900">₹{{ number_format($totalGross) }}</div>
            <span class="text-[10px] text-slate-400">Total earned before deductions</span>
        </div>
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Net Payable</span>
            <div class="mt-1 text-base font-bold text-blue-700">₹{{ number_format($totalNet) }}</div>
            <span class="text-[10px] text-slate-400">Total bank transfer obligations</span>
        </div>
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Paid Salaries</span>
            <div class="mt-1 text-base font-bold text-emerald-700">{{ $paidCount }} <span class="text-xs font-normal text-slate-500">Guards</span></div>
            <span class="text-[10px] text-emerald-600 font-semibold">Payment Disbursed</span>
        </div>
        <div class="bg-white p-3 rounded border border-slate-200 shadow-xs">
            <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Pending Disbursal</span>
            <div class="mt-1 text-base font-bold text-amber-700">{{ $pendingCount }} <span class="text-xs font-normal text-slate-500">Guards</span></div>
            <span class="text-[10px] text-amber-600 font-semibold">Awaiting Payment</span>
        </div>
    </div>

    <!-- FILTER & ACTION CONTROLS -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5 no-print">
        <div class="flex flex-wrap items-center gap-2">
            <!-- Period Selector -->
            <div class="flex items-center space-x-1.5">
                <label class="text-xs font-semibold text-slate-700">Period:</label>
                <select wire:model.live="month" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                    @endfor
                </select>
                <select wire:model.live="year" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                </select>
            </div>

            <!-- Status Filter -->
            <select wire:model.live="statusFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
            </select>
        </div>

        <div class="flex items-center space-x-2">
            <button wire:click="$set('showAdvanceModal', true)" class="px-2.5 py-1.5 rounded text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                + Grant Salary Advance
            </button>
            <button wire:click="generateMonthlyPayroll" wire:confirm="Generate payroll records for this month based on attendance?" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white flex items-center space-x-1.5 shadow-xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Generate Monthly Payroll</span>
            </button>
        </div>
    </div>

    <!-- PAYROLL TABLE -->
    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th>Guard Info</th>
                        <th>Present / Total Days</th>
                        <th>Basic Salary</th>
                        <th>Allowances</th>
                        <th>Overtime (OT)</th>
                        <th>Advance Recovery</th>
                        <th>Net Payable</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    @forelse ($payrolls as $p)
                        <tr class="hover:bg-slate-50/80 transition text-xs">
                            <td class="font-sans">
                                <div class="font-bold text-slate-900 text-xs">{{ $p->securityGuard->full_name ?? '—' }}</div>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $p->payroll_code }} &bull; {{ $p->securityGuard->guard_code ?? '' }}</span>
                            </td>
                            <td>
                                <span class="font-bold text-slate-900">{{ (float)$p->present_days }}</span>
                                <span class="text-slate-400 text-[10px]">/ {{ $p->total_working_days }} days</span>
                            </td>
                            <td>₹{{ number_format($p->basic_salary) }}</td>
                            <td>₹{{ number_format($p->allowances) }}</td>
                            <td>
                                @if ($p->overtime_amount > 0)
                                    <span class="text-indigo-700 font-semibold">₹{{ number_format($p->overtime_amount) }}</span>
                                    <span class="text-[10px] text-slate-400 block font-sans">({{ (float)$p->overtime_hours }}h @ ₹{{ (float)$p->overtime_rate }})</span>
                                @else
                                    <span class="text-slate-400">₹0</span>
                                @endif
                            </td>
                            <td>
                                @if ($p->advance_deductions > 0)
                                    <span class="text-rose-700 font-semibold">-₹{{ number_format($p->advance_deductions) }}</span>
                                @else
                                    <span class="text-slate-400">₹0</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-bold text-sm text-slate-900 font-mono">₹{{ number_format($p->net_salary) }}</span>
                            </td>
                            <td class="font-sans">
                                @if ($p->status === 'paid')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Paid ({{ $p->payment_mode }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap font-sans">
                                <button wire:click="viewSalarySlip({{ $p->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 mr-1 cursor-pointer">
                                    Salary Slip
                                </button>
                                @if ($p->status === 'pending')
                                    <button wire:click="openDisburseModal({{ $p->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer">
                                        Disburse
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-6 text-xs text-slate-500 font-sans">
                                No payroll generated for {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}. Click "Generate Monthly Payroll" to compute.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-2 border-t border-slate-200 bg-slate-50/50">
            {{ $payrolls->links() }}
        </div>
    </div>

    <!-- DISBURSE SALARY MODAL -->
    @if ($showPayModal && $selectedPayroll)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-md w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Record Salary Payment</h3>
                    <button wire:click="$set('showPayModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="disburseSalary" class="p-4 space-y-3 text-xs">
                    <div class="p-2.5 rounded bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900">{{ $selectedPayroll->securityGuard->full_name }}</span>
                            <span class="text-[10px] text-slate-500 block">Bank: {{ $selectedPayroll->securityGuard->bank_name ?? 'SBI' }} &bull; A/C: {{ $selectedPayroll->securityGuard->account_number ?? 'N/A' }}</span>
                        </div>
                        <span class="font-bold text-base text-emerald-700 font-mono">₹{{ number_format($selectedPayroll->net_salary) }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Payment Date *</label>
                            <input type="date" wire:model="payment_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Payment Mode *</label>
                            <select wire:model="payment_mode" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="bank_transfer">Bank Transfer / NEFT</option>
                                <option value="upi">UPI / Online</option>
                                <option value="cheque">Cheque</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Transaction Reference Number</label>
                        <input type="text" wire:model="payment_reference" placeholder="e.g. UTR / NEFT / Cheque No." class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Payment Remarks</label>
                        <input type="text" wire:model="payment_notes" placeholder="Optional comments..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showPayModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-emerald-700 hover:bg-emerald-800 text-white font-semibold">
                            Confirm Disbursal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- GRANT SALARY ADVANCE MODAL -->
    @if ($showAdvanceModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-md w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Grant Salary Advance</h3>
                    <button wire:click="$set('showAdvanceModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="applyAdvance" class="p-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Select Guard *</label>
                        <select wire:model="adv_guard_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="">-- Choose Guard --</option>
                            @foreach ($guards as $g)
                                <option value="{{ $g->id }}">{{ $g->full_name }} ({{ $g->guard_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Advance Amount (₹) *</label>
                        <input type="number" step="100" wire:model="adv_amount" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Purpose / Reason *</label>
                        <textarea wire:model="adv_reason" rows="2" required placeholder="Medical emergency, festival advance, family requirement..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showAdvanceModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            Approve Advance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- PRINTABLE SALARY SLIP MODAL -->
    @if ($showSlipModal && $selectedPayroll)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-2xl w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50 no-print">
                    <span class="font-bold text-xs text-slate-900 uppercase">Pay Slip: {{ $selectedPayroll->period }}</span>
                    <div class="flex items-center space-x-2">
                        <button onclick="window.print()" class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-900 text-white flex items-center space-x-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Print Slip</span>
                        </button>
                        <button wire:click="$set('showSlipModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                    </div>
                </div>

                <!-- Printable Pay Slip Content -->
                <div class="p-6 text-xs text-slate-800 space-y-4 print-break-inside-avoid">
                    <!-- Agency Header -->
                    <div class="text-center border-b pb-3">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-tight">{{ \App\Models\Setting::get('agency_name', 'Garuda Apex Security Services Pvt. Ltd.') }}</h2>
                        <p class="text-[11px] text-slate-600">Plot 42, G.S. Road, Christian Basti, Guwahati, Assam - 781005 &bull; Reg: 18AABCG1234F1Z8</p>
                        <p class="text-[11px] font-bold text-slate-800 mt-1 uppercase underline tracking-wider">Salary Pay Slip for {{ $selectedPayroll->period }}</p>
                    </div>

                    <!-- Guard & Payment Details Grid -->
                    <div class="grid grid-cols-2 gap-3 p-2.5 bg-slate-50 border border-slate-200 rounded text-[11px]">
                        <div class="space-y-1">
                            <div><span class="text-slate-500">Guard Name:</span> <span class="font-bold text-slate-900">{{ $selectedPayroll->securityGuard->full_name }}</span></div>
                            <div><span class="text-slate-500">Guard ID:</span> <span class="font-mono font-semibold">{{ $selectedPayroll->securityGuard->guard_code }}</span></div>
                            <div><span class="text-slate-500">Designation:</span> <span>{{ $selectedPayroll->securityGuard->designation }}</span></div>
                            <div><span class="text-slate-500">Bank Name:</span> <span>{{ $selectedPayroll->securityGuard->bank_name ?? 'SBI' }}</span></div>
                            <div><span class="text-slate-500">Bank A/C:</span> <span class="font-mono">{{ $selectedPayroll->securityGuard->account_number ?? '—' }}</span></div>
                        </div>
                        <div class="space-y-1">
                            <div><span class="text-slate-500">Slip No:</span> <span class="font-mono">{{ $selectedPayroll->payroll_code }}</span></div>
                            <div><span class="text-slate-500">Working Days:</span> <span>{{ $selectedPayroll->total_working_days }} Days</span></div>
                            <div><span class="text-slate-500">Present Days:</span> <span class="font-bold">{{ (float)$selectedPayroll->present_days }} Days</span></div>
                            <div><span class="text-slate-500">Payment Status:</span> <span class="font-bold capitalize text-emerald-700">{{ $selectedPayroll->status }}</span></div>
                            <div><span class="text-slate-500">Disbursal Ref:</span> <span class="font-mono">{{ $selectedPayroll->payment_reference ?? 'Pending' }}</span></div>
                        </div>
                    </div>

                    <!-- Earnings and Deductions Table -->
                    <div class="border border-slate-300 rounded overflow-hidden">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate-100 border-b border-slate-300 font-bold text-slate-700">
                                    <th class="p-2 text-left w-1/2">Earnings (Credits)</th>
                                    <th class="p-2 text-right">Amount (₹)</th>
                                    <th class="p-2 text-left border-l border-slate-300 w-1/2">Deductions (Debits)</th>
                                    <th class="p-2 text-right">Amount (₹)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr>
                                    <td class="p-2">Basic Salary Earned</td>
                                    <td class="p-2 text-right font-mono">₹{{ number_format($selectedPayroll->basic_salary, 2) }}</td>
                                    <td class="p-2 border-l border-slate-200">Salary Advance Recovery</td>
                                    <td class="p-2 text-right font-mono text-rose-700">₹{{ number_format($selectedPayroll->advance_deductions, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="p-2">Special Duty Allowances</td>
                                    <td class="p-2 text-right font-mono">₹{{ number_format($selectedPayroll->allowances, 2) }}</td>
                                    <td class="p-2 border-l border-slate-200">Leave Deductions / LOP</td>
                                    <td class="p-2 text-right font-mono text-rose-700">₹{{ number_format($selectedPayroll->leave_deductions, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="p-2">Overtime Duty ({{ (float)$selectedPayroll->overtime_hours }} hrs)</td>
                                    <td class="p-2 text-right font-mono">₹{{ number_format($selectedPayroll->overtime_amount, 2) }}</td>
                                    <td class="p-2 border-l border-slate-200">Other Deductions / Uniform</td>
                                    <td class="p-2 text-right font-mono text-rose-700">₹{{ number_format($selectedPayroll->other_deductions, 2) }}</td>
                                </tr>
                                <tr class="bg-slate-50 font-bold">
                                    <td class="p-2">Total Gross Earnings</td>
                                    <td class="p-2 text-right font-mono">₹{{ number_format($selectedPayroll->gross_salary, 2) }}</td>
                                    <td class="p-2 border-l border-slate-200">Total Deductions</td>
                                    <td class="p-2 text-right font-mono text-rose-700">₹{{ number_format($selectedPayroll->advance_deductions + $selectedPayroll->leave_deductions + $selectedPayroll->other_deductions, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Net Salary Highlight -->
                    <div class="p-3 bg-slate-900 text-white rounded flex items-center justify-between">
                        <div>
                            <span class="text-xs uppercase tracking-wider text-slate-300">Net Take-Home Salary</span>
                            <span class="text-[10px] text-slate-400 block">Subject to applicable Indian labour regulations</span>
                        </div>
                        <span class="text-xl font-bold font-mono text-amber-400">₹{{ number_format($selectedPayroll->net_salary, 2) }}</span>
                    </div>

                    <!-- Signatures -->
                    <div class="pt-8 flex justify-between text-center text-xs">
                        <div>
                            <div class="w-36 border-t border-slate-400 mx-auto"></div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Guard's Signature</span>
                        </div>
                        <div>
                            <div class="w-36 border-t border-slate-400 mx-auto"></div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Authorized Signatory / Accountant</span>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-2.5 border-t border-slate-200 bg-slate-50 flex justify-end no-print">
                    <button wire:click="$set('showSlipModal', false)" class="px-3 py-1 bg-slate-200 hover:bg-slate-300 rounded text-xs font-medium text-slate-700">Close</button>
                </div>
            </div>
        </div>
    @endif

</div>
