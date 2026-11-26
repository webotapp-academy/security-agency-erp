<div class="space-y-3.5">

    <!-- TOP CONTROLS & FILTER BAR -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5">
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative w-56">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search guard name, code..."
                       class="w-full pl-8 pr-2.5 py-1 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Status Filter -->
            <select wire:model.live="statusFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="">All Applications</option>
                <option value="pending">Pending Review</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        <button wire:click="openApplyModal" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white flex items-center space-x-1.5 shadow-xs cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Apply for Leave</span>
        </button>
    </div>

    <!-- LEAVES TABLE -->
    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th>Guard Info</th>
                        <th>Leave Type</th>
                        <th>Duration & Dates</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Approval Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($leaves as $leave)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>
                                <div class="font-bold text-slate-900 text-xs">{{ $leave->securityGuard->full_name ?? '—' }}</div>
                                <span class="text-[10px] font-mono text-slate-400">{{ $leave->securityGuard->guard_code ?? '' }}</span>
                            </td>
                            <td>
                                <span class="font-medium text-slate-800 text-xs capitalize">{{ $leave->leave_type }} Leave</span>
                            </td>
                            <td>
                                <div class="text-xs text-slate-800 font-medium font-mono">
                                    {{ $leave->start_date->format('d M y') }} - {{ $leave->end_date->format('d M y') }}
                                </div>
                            </td>
                            <td>
                                <span class="font-bold text-slate-900 text-xs">{{ $leave->days_count }} Days</span>
                            </td>
                            <td>
                                <p class="text-xs text-slate-600 max-w-xs truncate">{{ $leave->reason }}</p>
                                @if ($leave->rejection_reason)
                                    <span class="text-[10px] text-rose-600 block mt-0.5 font-medium">Rejection Note: {{ $leave->rejection_reason }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $leaveBadge = [
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border capitalize {{ $leaveBadge[$leave->status] ?? '' }}">
                                    {{ $leave->status }}
                                </span>
                                @if ($leave->approvedBy)
                                    <span class="text-[10px] text-slate-400 block">by {{ $leave->approvedBy->name }}</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                @if ($leave->status === 'pending')
                                    <button wire:click="approveLeave({{ $leave->id }})" class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 mr-1 cursor-pointer">
                                        Approve
                                    </button>
                                    <button wire:click="openRejectModal({{ $leave->id }})" class="px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 cursor-pointer">
                                        Reject
                                    </button>
                                @else
                                    <span class="text-slate-400 text-xs">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-xs text-slate-500">No leave applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-2 border-t border-slate-200 bg-slate-50/50">
            {{ $leaves->links() }}
        </div>
    </div>

    <!-- APPLY LEAVE MODAL -->
    @if ($showApplyModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-md w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Submit Leave Application</h3>
                    <button wire:click="$set('showApplyModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="applyLeave" class="p-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Select Guard *</label>
                        <select wire:model="guard_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="">-- Choose Guard --</option>
                            @foreach ($guards as $g)
                                <option value="{{ $g->id }}">{{ $g->full_name }} ({{ $g->guard_code }})</option>
                            @endforeach
                        </select>
                        @error('guard_id') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Leave Type *</label>
                        <select wire:model="leave_type" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="casual">Casual Leave</option>
                            <option value="sick">Sick Leave</option>
                            <option value="earned">Earned Leave</option>
                            <option value="unpaid">Loss of Pay / Unpaid</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Start Date *</label>
                            <input type="date" wire:model="start_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">End Date *</label>
                            <input type="date" wire:model="end_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Reason for Leave *</label>
                        <textarea wire:model="reason" rows="2" required placeholder="State purpose of leave..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                        @error('reason') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showApplyModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- REJECTION REASON MODAL -->
    @if ($showActionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-sm w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Reject Leave Request</h3>
                    <button wire:click="$set('showActionModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="rejectLeave" class="p-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Reason for Rejection *</label>
                        <textarea wire:model="rejectionReason" rows="3" required placeholder="e.g. Critical site staffing shortage, lack of replacement..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                        @error('rejectionReason') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showActionModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-rose-700 hover:bg-rose-800 text-white font-semibold">
                            Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
