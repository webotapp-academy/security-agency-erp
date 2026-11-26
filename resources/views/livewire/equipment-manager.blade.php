<div class="space-y-3.5">

    <!-- TOP CONTROLS & TABS BAR -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5">
        <div class="flex flex-wrap items-center gap-2">
            <!-- Tab switcher -->
            <div class="inline-flex rounded border border-slate-300 p-0.5 bg-slate-100 text-xs font-semibold">
                <button wire:click="$set('activeTab', 'assignments')" class="px-2.5 py-1 rounded {{ $activeTab === 'assignments' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Issued Equipment Register
                </button>
                <button wire:click="$set('activeTab', 'inventory')" class="px-2.5 py-1 rounded {{ $activeTab === 'inventory' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Stock Inventory ({{ $inventory->count() }})
                </button>
            </div>

            @if ($activeTab === 'assignments')
                <div class="relative w-52">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search guard or item..."
                           class="w-full pl-8 pr-2.5 py-1 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <select wire:model.live="statusFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                    <option value="">All Assignment Statuses</option>
                    <option value="issued">Currently Issued</option>
                    <option value="returned">Returned Safe</option>
                    <option value="lost">Lost</option>
                    <option value="damaged">Damaged</option>
                </select>
            @endif
        </div>

        <div class="flex items-center space-x-2">
            <button wire:click="openItemModal" class="px-2.5 py-1.5 rounded text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                + Add Item to Stock
            </button>
            <button wire:click="openIssueModal" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white flex items-center space-x-1.5 shadow-xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Issue Gear to Guard</span>
            </button>
        </div>
    </div>

    @if ($activeTab === 'assignments')
        <!-- ASSIGNMENTS TABLE -->
        <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
            <div class="px-4 py-2.5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <span class="font-bold text-xs text-slate-900">Issued Equipment & Uniforms Register</span>
                <span class="text-[11px] text-slate-500 font-medium">Tracking gear handed out to personnel</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left compact-table border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                            <th>Guard Name</th>
                            <th>Equipment Item</th>
                            <th>Qty</th>
                            <th>Issue Date & Condition</th>
                            <th>Return Info</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($assignments as $asn)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td>
                                    <div class="font-bold text-slate-900 text-xs">{{ $asn->securityGuard->full_name ?? '—' }}</div>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $asn->securityGuard->guard_code ?? '' }}</span>
                                </td>
                                <td>
                                    <div class="font-bold text-slate-900 text-xs">{{ $asn->equipmentItem->name ?? '—' }}</div>
                                    <span class="text-[10px] text-slate-500 font-mono">{{ $asn->equipmentItem->item_code ?? '' }}</span>
                                </td>
                                <td>
                                    <span class="font-bold text-slate-900 text-xs">{{ $asn->quantity }} pcs</span>
                                </td>
                                <td>
                                    <div class="text-xs text-slate-800 font-medium">{{ $asn->issue_date->format('d M Y') }}</div>
                                    <span class="text-[10px] text-slate-400">Condition: {{ $asn->condition_on_issue }}</span>
                                </td>
                                <td>
                                    @if ($asn->return_date)
                                        <div class="text-xs text-slate-700 font-medium">{{ $asn->return_date->format('d M Y') }}</div>
                                        <span class="text-[10px] text-slate-500">{{ $asn->condition_on_return ?? 'Returned' }}</span>
                                        @if ($asn->penalty_amount > 0)
                                            <span class="text-[10px] text-rose-600 font-bold block">Penalty: ₹{{ number_format($asn->penalty_amount) }}</span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 text-xs">With Guard</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $eqClass = [
                                            'issued' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'returned' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'lost' => 'bg-rose-50 text-rose-700 border-rose-200 font-bold',
                                            'damaged' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border capitalize {{ $eqClass[$asn->status] ?? '' }}">
                                        {{ $asn->status }}
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    @if ($asn->status === 'issued')
                                        <button wire:click="openReturnModal({{ $asn->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                                            Return / Settle
                                        </button>
                                    @else
                                        <span class="text-slate-400 text-xs">Settled</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-xs text-slate-500">No equipment assignment records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-2 border-t border-slate-200 bg-slate-50/50">
                {{ $assignments->links() }}
            </div>
        </div>
    @else
        <!-- INVENTORY STOCK CARDS & TABLE -->
        <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
            <div class="px-4 py-2.5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <span class="font-bold text-xs text-slate-900">Agency Equipment Stock Inventory</span>
                <span class="text-[11px] text-slate-500 font-medium">Uniforms, Boots, Badges, Searchlights, Batons</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left compact-table border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                            <th>Item Code & Name</th>
                            <th>Category</th>
                            <th>Total Stock</th>
                            <th>In Warehouse (Available)</th>
                            <th>Issued to Personnel</th>
                            <th>Unit Cost</th>
                            <th>Condition</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($inventory as $item)
                            @php
                                $issuedQty = $item->total_quantity - $item->available_quantity;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td>
                                    <div class="font-bold text-slate-900 text-xs">{{ $item->name }}</div>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $item->item_code }}</span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-medium bg-slate-100 text-slate-700 capitalize">
                                        {{ str_replace('_', ' ', $item->category) }}
                                    </span>
                                </td>
                                <td><span class="font-bold text-slate-900 text-xs">{{ $item->total_quantity }}</span></td>
                                <td>
                                    <span class="font-bold text-xs {{ $item->available_quantity > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $item->available_quantity }} Available
                                    </span>
                                </td>
                                <td>
                                    <span class="font-medium text-slate-700 text-xs">{{ $issuedQty }} In Duty</span>
                                </td>
                                <td><span class="font-mono text-xs text-slate-800">₹{{ number_format($item->unit_cost) }}</span></td>
                                <td><span class="text-xs text-slate-600">{{ $item->condition_status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ISSUE EQUIPMENT MODAL -->
    @if ($showIssueModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-md w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Issue Equipment / Uniform</h3>
                    <button wire:click="$set('showIssueModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="issueEquipment" class="p-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Select Equipment Item *</label>
                        <select wire:model="issue_item_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            @foreach ($inventory as $invItem)
                                <option value="{{ $invItem->id }}" {{ $invItem->available_quantity <= 0 ? 'disabled' : '' }}>
                                    {{ $invItem->name }} ({{ $invItem->available_quantity }} in stock)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Select Guard *</label>
                        <select wire:model="issue_guard_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            @foreach ($guards as $g)
                                <option value="{{ $g->id }}">{{ $g->full_name }} ({{ $g->guard_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Quantity *</label>
                            <input type="number" min="1" wire:model="issue_quantity" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            @error('issue_quantity') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Issue Date *</label>
                            <input type="date" wire:model="issue_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Condition at Issue</label>
                        <input type="text" wire:model="condition_on_issue" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Notes / Serial No.</label>
                        <input type="text" wire:model="issue_notes" placeholder="e.g. Size 42, Serial #..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showIssueModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            Confirm Issue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- RETURN MODAL -->
    @if ($showReturnModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-sm w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Process Return / Loss</h3>
                    <button wire:click="$set('showReturnModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="processReturn" class="p-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Return Status *</label>
                        <select wire:model="return_status" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-semibold">
                            <option value="returned">Returned in Good Condition (Restock)</option>
                            <option value="damaged">Damaged / Torn</option>
                            <option value="lost">Lost by Guard</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Return Date *</label>
                            <input type="date" wire:model="return_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Condition Notes</label>
                            <input type="text" wire:model="condition_on_return" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Penalty / Replacement Charge (₹)</label>
                        <input type="number" step="10" wire:model="penalty_amount" placeholder="0 if returned normal" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Remarks</label>
                        <input type="text" wire:model="return_notes" placeholder="Optional notes" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showReturnModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            Update Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ADD INVENTORY ITEM MODAL -->
    @if ($showItemModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-sm w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Add Item to Inventory</h3>
                    <button wire:click="$set('showItemModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="createItem" class="p-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Item Name *</label>
                        <input type="text" wire:model="item_name" required placeholder="e.g. Winter Security Parka" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Item Code *</label>
                            <input type="text" wire:model="item_code" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono uppercase">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Category</label>
                            <select wire:model="item_category" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="uniform">Uniform</option>
                                <option value="shoes">Shoes / Boots</option>
                                <option value="torch">LED Torch</option>
                                <option value="baton">Baton / Lathi</option>
                                <option value="communication">Walkie-Talkie</option>
                                <option value="raincoat">Raincoat</option>
                                <option value="safety_gear">Safety Gear</option>
                                <option value="id_card">ID Badge</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Stock Quantity *</label>
                            <input type="number" min="1" wire:model="total_quantity" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Unit Cost (₹) *</label>
                            <input type="number" step="10" wire:model="unit_cost" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showItemModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            Add to Inventory
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
