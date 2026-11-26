<div class="space-y-3.5">

    <!-- TOP CONTROLS & FILTER BAR -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5">
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative w-56">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search client, company, phone..."
                       class="w-full pl-8 pr-2.5 py-1 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select wire:model.live="statusFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <button wire:click="openCreateModal" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white flex items-center space-x-1.5 shadow-xs cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add New Client</span>
        </button>
    </div>

    <!-- CLIENTS TABLE -->
    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th>Client / Company</th>
                        <th>Contact Person</th>
                        <th>Active Sites</th>
                        <th>Contract Period</th>
                        <th>Annual Value</th>
                        <th>Outstanding Balance</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clients as $cl)
                        @php
                            $totalAssignedGuards = $cl->sites->sum(function($s) { return $s->activeDeployments->count(); });
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>
                                <div class="font-bold text-slate-900 text-xs">{{ $cl->client_name }}</div>
                                <div class="text-[10px] text-slate-500">{{ $cl->client_code }} &bull; {{ $cl->company_name ?? 'Individual' }}</div>
                            </td>
                            <td>
                                <div class="text-xs text-slate-800 font-medium">{{ $cl->contact_person }}</div>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $cl->phone }}</div>
                            </td>
                            <td>
                                <span class="font-bold text-slate-900 text-xs">{{ $cl->sites->count() }} Sites</span>
                                <span class="text-[10px] text-slate-500 block">({{ $totalAssignedGuards }} guards deployed)</span>
                            </td>
                            <td>
                                @if($cl->contract_start_date && $cl->contract_end_date)
                                    <div class="text-xs text-slate-700 font-medium">
                                        {{ $cl->contract_start_date->format('d M y') }} - {{ $cl->contract_end_date->format('d M y') }}
                                    </div>
                                    @if ($cl->is_contract_expiring_soon)
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Expiring Soon
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-bold text-slate-900 text-xs">₹{{ number_format($cl->contract_amount) }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $cl->payment_terms }}</span>
                            </td>
                            <td>
                                @if ($cl->outstanding_balance > 0)
                                    <span class="font-bold text-rose-700 text-xs">₹{{ number_format($cl->outstanding_balance) }}</span>
                                @else
                                    <span class="text-emerald-700 font-semibold text-xs">₹0.00 (Clear)</span>
                                @endif
                            </td>
                            <td>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border capitalize {{ $cl->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    {{ $cl->status }}
                                </span>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <button wire:click="viewDetails({{ $cl->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 mr-1 cursor-pointer">
                                    Profile
                                </button>
                                <button wire:click="openEditModal({{ $cl->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-xs text-slate-500">No clients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-2 border-t border-slate-200 bg-slate-50/50">
            {{ $clients->links() }}
        </div>
    </div>

    <!-- CREATE / EDIT MODAL -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">
                        {{ $isEditing ? 'Edit Client: ' . $client_code : 'Add New Client / Organization' }}
                    </h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="save" class="overflow-y-auto p-4 space-y-3 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Client Code</label>
                            <input type="text" wire:model="client_code" readonly class="w-full px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono text-slate-600">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Client Name *</label>
                            <input type="text" wire:model="client_name" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            @error('client_name') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-medium text-slate-700 mb-0.5">Company / Legal Entity Name</label>
                            <input type="text" wire:model="company_name" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Contact Person *</label>
                            <input type="text" wire:model="contact_person" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Contact Phone *</label>
                            <input type="text" wire:model="phone" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Email Address</label>
                            <input type="email" wire:model="email" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">GSTIN Number</label>
                            <input type="text" wire:model="gstin" placeholder="18AAB..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono uppercase">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-medium text-slate-700 mb-0.5">Billing Address</label>
                            <textarea wire:model="billing_address" rows="2" class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">State</label>
                            <input type="text" wire:model="state" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">PIN Code</label>
                            <input type="text" wire:model="pincode" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Contract Start Date</label>
                            <input type="date" wire:model="contract_start_date" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Contract End Date</label>
                            <input type="date" wire:model="contract_end_date" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Annual Contract Value (₹) *</label>
                            <input type="number" step="0.01" wire:model="contract_amount" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Payment Terms</label>
                            <select wire:model="payment_terms" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="Due on Receipt">Due on Receipt</option>
                                <option value="Net 15">Net 15 Days</option>
                                <option value="Net 30">Net 30 Days</option>
                                <option value="Net 45">Net 45 Days</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Status</label>
                            <select wire:model="status" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-medium text-slate-700 mb-0.5">Special Terms / Notes</label>
                            <textarea wire:model="notes" rows="2" class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            {{ $isEditing ? 'Save Client Changes' : 'Register Client' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- DETAIL MODAL -->
    @if ($showDetailModal && $selectedClient)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center space-x-2">
                        <span class="font-bold text-xs text-slate-900 uppercase">Client Dossier: {{ $selectedClient->client_name }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-200 text-slate-800">{{ $selectedClient->client_code }}</span>
                    </div>
                    <button wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <div class="overflow-y-auto p-4 space-y-4 text-xs">
                    <!-- Client Summary Header -->
                    <div class="p-3 bg-slate-50 rounded border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div>
                            <span class="text-slate-500 block text-[11px]">Primary Contact</span>
                            <span class="font-bold text-slate-900">{{ $selectedClient->contact_person }}</span>
                            <span class="block text-slate-600 font-mono text-[11px]">{{ $selectedClient->phone }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[11px]">GSTIN & State</span>
                            <span class="font-mono font-semibold text-slate-800">{{ $selectedClient->gstin ?? 'Unregistered' }}</span>
                            <span class="block text-slate-600 text-[11px]">{{ $selectedClient->state }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[11px]">Outstanding Dues</span>
                            <span class="font-bold text-base {{ $selectedClient->outstanding_balance > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                ₹{{ number_format($selectedClient->outstanding_balance) }}
                            </span>
                        </div>
                    </div>

                    <!-- Associated Deployment Sites -->
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs border-b border-slate-200 pb-1 mb-2">
                            Associated Security Deployment Sites ({{ $selectedClient->sites->count() }})
                        </h4>
                        <div class="space-y-2">
                            @forelse ($selectedClient->sites as $site)
                                <div class="p-2.5 rounded border border-slate-200 bg-white">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="font-bold text-slate-900">{{ $site->site_name }}</span>
                                            <span class="text-[10px] text-slate-500 font-mono">({{ $site->site_code }}) &bull; {{ $site->site_type }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-800">
                                            {{ $site->activeDeployments->count() }} / {{ $site->required_guards }} Guards Deployed
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-600 mt-1"><span class="font-medium text-slate-700">Instructions:</span> {{ $site->duty_instructions ?? 'Standard security duty' }}</p>
                                </div>
                            @empty
                                <p class="text-slate-500 text-xs py-2">No deployment sites registered for this client yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Billing History -->
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs border-b border-slate-200 pb-1 mb-2">Recent Invoices</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left compact-table border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                                        <th>Invoice #</th>
                                        <th>Period</th>
                                        <th>Amount</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($selectedClient->invoices as $inv)
                                        <tr class="border-b border-slate-100">
                                            <td class="font-mono font-medium">{{ $inv->invoice_number }}</td>
                                            <td>{{ $inv->billing_period }}</td>
                                            <td class="font-bold">₹{{ number_format($inv->total_amount) }}</td>
                                            <td class="text-emerald-700">₹{{ number_format($inv->paid_amount) }}</td>
                                            <td class="text-rose-700 font-semibold">₹{{ number_format($inv->balance_amount) }}</td>
                                            <td><span class="capitalize text-[10px] font-semibold">{{ $inv->status }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center py-2 text-slate-400">No invoices generated yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-2.5 border-t border-slate-200 bg-slate-50 flex justify-end">
                    <button wire:click="$set('showDetailModal', false)" class="px-3 py-1 bg-slate-200 hover:bg-slate-300 rounded text-xs font-medium text-slate-700">Close</button>
                </div>
            </div>
        </div>
    @endif

</div>
