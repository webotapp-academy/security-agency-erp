<div class="space-y-3.5">
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5">
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative w-48">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search payee, code..."
                       class="w-full pl-8 pr-2.5 py-1 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select wire:model.live="categoryFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded">
                <option value="">All Categories</option>
                <option value="uniform_expenses">Uniform Expenses</option>
                <option value="equipment_expenses">Equipment Expenses</option>
                <option value="transportation">Transportation / Fuel</option>
                <option value="recruitment">Recruitment</option>
                <option value="office_rent">Office Rent</option>
                <option value="electricity">Electricity</option>
                <option value="staff_salaries">Staff Salaries</option>
                <option value="other">Other</option>
            </select>

            <input type="date" wire:model.live="dateFrom" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded">
            <span class="text-xs text-slate-400">to</span>
            <input type="date" wire:model.live="dateTo" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded">
        </div>

        <div class="flex items-center space-x-3">
            <div class="text-right">
                <span class="text-[10px] text-slate-500 uppercase block">Filtered Total</span>
                <span class="font-bold text-sm text-rose-700">₹{{ number_format($totalFiltered, 2) }}</span>
            </div>
            <button wire:click="openCreateModal" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white cursor-pointer">
                + Record Expense
            </button>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th>Voucher # & Date</th>
                        <th>Category</th>
                        <th>Payee / Vendor</th>
                        <th>Description</th>
                        <th>Mode & Ref</th>
                        <th class="text-right">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($expenses as $exp)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>
                                <div class="font-bold text-slate-900 text-xs font-mono">{{ $exp->expense_code }}</div>
                                <span class="text-[10px] text-slate-500">{{ $exp->expense_date->format('d M Y') }}</span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 capitalize">
                                    {{ str_replace('_', ' ', $exp->category) }}
                                </span>
                            </td>
                            <td class="font-medium text-slate-900 text-xs">{{ $exp->payee }}</td>
                            <td class="text-xs text-slate-600 max-w-xs truncate">{{ $exp->description ?? '—' }}</td>
                            <td>
                                <span class="text-xs text-slate-700 capitalize">{{ str_replace('_', ' ', $exp->payment_mode) }}</span>
                                <span class="text-[10px] text-slate-400 font-mono block">{{ $exp->reference_number ?? '' }}</span>
                            </td>
                            <td class="text-right font-bold text-slate-900 font-mono">₹{{ number_format($exp->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-6 text-xs text-slate-500">No expenses found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-2 border-t border-slate-200 bg-slate-50/50">{{ $expenses->links() }}</div>
    </div>

    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-md w-full overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Record Agency Expense</h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>
                <form wire:submit="saveExpense" class="p-4 space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Voucher Code</label>
                            <input type="text" wire:model="expense_code" readonly class="w-full px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Date *</label>
                            <input type="date" wire:model="expense_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Category *</label>
                            <select wire:model="category" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="uniform_expenses">Uniform Expenses</option>
                                <option value="equipment_expenses">Equipment Expenses</option>
                                <option value="transportation">Transportation / Fuel</option>
                                <option value="recruitment">Recruitment</option>
                                <option value="office_rent">Office Rent</option>
                                <option value="electricity">Electricity</option>
                                <option value="staff_salaries">Staff Salaries</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Amount (₹) *</label>
                            <input type="number" step="0.01" wire:model="amount" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-bold">
                        </div>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Payee / Vendor Name *</label>
                        <input type="text" wire:model="payee" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Payment Mode</label>
                            <select wire:model="payment_mode" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="upi">UPI</option>
                                <option value="cash">Cash</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Reference / Receipt #</label>
                            <input type="text" wire:model="reference_number" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Description</label>
                        <textarea wire:model="description" rows="2" class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                    </div>
                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 text-white font-semibold">Save Expense</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
