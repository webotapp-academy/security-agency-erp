<div class="space-y-3.5">

    <!-- TOP CONTROLS & FILTER BAR -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5 no-print">
        <div class="flex flex-wrap items-center gap-2">
            <!-- Date picker -->
            <div class="flex items-center space-x-1">
                <span class="text-xs font-medium text-slate-500">Date:</span>
                <input type="date" wire:model.live="selectedDate"
                       class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white font-mono">
            </div>

            <!-- Site Filter -->
            <select wire:model.live="selectedSiteId" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="">All Deployment Sites</option>
                @foreach ($sites as $s)
                    <option value="{{ $s->id }}">{{ $s->site_name }}</option>
                @endforeach
            </select>

            <!-- View Mode Toggle -->
            <div class="inline-flex rounded border border-slate-300 bg-slate-50 p-0.5 text-xs">
                <button wire:click="$set('viewMode', 'daily')" class="px-2 py-0.5 rounded font-medium {{ $viewMode === 'daily' ? 'bg-white shadow-xs font-bold text-slate-900' : 'text-slate-600' }}">Daily View</button>
                <button wire:click="$set('viewMode', 'weekly')" class="px-2 py-0.5 rounded font-medium {{ $viewMode === 'weekly' ? 'bg-white shadow-xs font-bold text-slate-900' : 'text-slate-600' }}">Weekly Grid</button>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-2.5 py-1.5 rounded text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center space-x-1 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print</span>
            </button>
            <button wire:click="generateRosterForDate" class="px-2.5 py-1.5 rounded text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 cursor-pointer">
                Auto-Populate from Deployments
            </button>
            <button wire:click="$set('showShiftModal', true)" class="px-2.5 py-1.5 rounded text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                + Shift
            </button>
            <button wire:click="openAddSlotModal" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white flex items-center space-x-1 shadow-xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Slot</span>
            </button>
        </div>
    </div>

    <!-- SHIFTS REFERENCE PILLS -->
    <div class="flex items-center space-x-2 text-xs overflow-x-auto pb-1 no-print">
        <span class="text-slate-400 text-[11px] font-semibold uppercase">Configured Shifts:</span>
        @foreach ($shifts as $sh)
            <span class="px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-700 text-[11px] flex-shrink-0">
                <strong class="text-slate-900">{{ $sh->name }}</strong> ({{ $sh->formatted_time }})
            </span>
        @endforeach
    </div>

    @if ($viewMode === 'daily')
        <!-- DAILY ROSTER TABLE -->
        <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
            <div class="px-4 py-2.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="font-bold text-xs text-slate-900">
                    Daily Security Roster for {{ \Carbon\Carbon::parse($selectedDate)->format('l, d F Y') }}
                </span>
                <span class="text-[11px] text-slate-500 font-mono">{{ $dailySlots->count() }} Duty Slots</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left compact-table border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                            <th>Site & Client</th>
                            <th>Shift / Hours</th>
                            <th>Assigned Guard</th>
                            <th>Replacement Guard</th>
                            <th>Slot Status</th>
                            <th>Instructions / Notes</th>
                            <th class="text-right no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($dailySlots as $slot)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td>
                                    <div class="font-bold text-slate-900 text-xs">{{ $slot->site->site_name ?? '—' }}</div>
                                    <span class="text-[10px] text-slate-500">{{ $slot->site->client->client_name ?? '' }}</span>
                                </td>
                                <td>
                                    <span class="font-semibold text-slate-800 text-xs">{{ $slot->shift->name ?? 'General' }}</span>
                                    <span class="text-[10px] font-mono text-slate-500 block">{{ $slot->shift->formatted_time ?? '' }}</span>
                                </td>
                                <td>
                                    <div class="font-bold text-slate-900 text-xs">{{ $slot->securityGuard->full_name ?? '—' }}</div>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $slot->securityGuard->guard_code ?? '' }}</span>
                                </td>
                                <td>
                                    @if ($slot->replacementGuard)
                                        <div class="font-bold text-purple-700 text-xs">{{ $slot->replacementGuard->full_name }}</div>
                                        <span class="text-[10px] text-purple-600 font-mono">Replaced Original Guard</span>
                                    @else
                                        <span class="text-slate-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusBadge = [
                                            'scheduled' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'absent' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'replaced' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border capitalize {{ $statusBadge[$slot->status] ?? '' }}">
                                        {{ $slot->status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-slate-600 text-xs">{{ $slot->notes ?? 'Standard Duty' }}</span>
                                </td>
                                <td class="text-right whitespace-nowrap no-print">
                                    <button wire:click="openEditSlotModal({{ $slot->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 mr-1 cursor-pointer">
                                        Edit / Replace
                                    </button>
                                    <button wire:click="deleteSlot({{ $slot->id }})" wire:confirm="Remove this roster slot?" class="px-2 py-0.5 rounded text-[11px] font-medium text-rose-600 hover:bg-rose-50 cursor-pointer">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-xs text-slate-500">
                                    No duty roster slots scheduled for this date. Click "Auto-Populate from Deployments" or "Add Slot".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- WEEKLY GRID VIEW -->
        <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
            <div class="px-4 py-2.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="font-bold text-xs text-slate-900">
                    Weekly Duty Matrix: {{ $weekDates[0]->format('d M') }} to {{ $weekDates[6]->format('d M Y') }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left compact-table border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-semibold text-[11px] uppercase border-b border-slate-200">
                            @foreach ($weekDates as $day)
                                <th class="text-center {{ $day->isToday() ? 'bg-amber-50 text-amber-900' : '' }}">
                                    {{ $day->format('D') }}<br>
                                    <span class="font-mono text-[10px]">{{ $day->format('d M') }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            @foreach ($weekDates as $day)
                                @php
                                    $dayKey = $day->format('Y-m-d');
                                    $daySlots = $weeklySlots[$dayKey] ?? collect();
                                @endphp
                                <td class="align-top p-2 {{ $day->isToday() ? 'bg-amber-50/30' : '' }}">
                                    <div class="space-y-1.5 min-h-[120px]">
                                        @forelse ($daySlots as $s)
                                            <div class="p-1.5 bg-slate-50 border border-slate-200 rounded text-[11px]">
                                                <div class="font-bold text-slate-900 truncate">{{ $s->securityGuard->full_name ?? 'Guard' }}</div>
                                                <div class="text-[10px] text-slate-500 font-mono">{{ $s->shift->name ?? '' }}</div>
                                                @if($s->replacementGuard)
                                                    <div class="text-[9px] text-purple-700 font-semibold">Rep: {{ $s->replacementGuard->full_name }}</div>
                                                @endif
                                            </div>
                                        @empty
                                            <span class="text-[10px] text-slate-400 block text-center py-4">No slots</span>
                                        @endforelse
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ADD / EDIT SLOT MODAL -->
    @if ($showAssignModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-md w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">
                        {{ $roster_id ? 'Edit Duty Slot' : 'Assign Duty Slot' }}
                    </h3>
                    <button wire:click="$set('showAssignModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="saveSlot" class="p-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Deployment Site *</label>
                        <select wire:model="site_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            @foreach ($sites as $s)
                                <option value="{{ $s->id }}">{{ $s->site_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Date *</label>
                            <input type="date" wire:model="roster_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Shift *</label>
                            <select wire:model="shift_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                @foreach ($shifts as $sh)
                                    <option value="{{ $sh->id }}">{{ $sh->name }} ({{ $sh->formatted_time }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Assigned Guard *</label>
                        <select wire:model="guard_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="">-- Select Guard --</option>
                            @foreach ($guards as $g)
                                <option value="{{ $g->id }}">{{ $g->full_name }} ({{ $g->guard_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Replacement Guard (If applicable)</label>
                        <select wire:model="replacement_guard_id" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="">-- None (Original Guard on duty) --</option>
                            @foreach ($guards as $g)
                                <option value="{{ $g->id }}">{{ $g->full_name }} ({{ $g->guard_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Slot Status</label>
                        <select wire:model="status" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="scheduled">Scheduled</option>
                            <option value="completed">Completed</option>
                            <option value="absent">Absent</option>
                            <option value="replaced">Replaced</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Instructions / Notes</label>
                        <input type="text" wire:model="notes" placeholder="Specific post instructions..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showAssignModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            Save Slot
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- CUSTOM SHIFT MODAL -->
    @if ($showShiftModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-sm w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Create Custom Shift</h3>
                    <button wire:click="$set('showShiftModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="saveCustomShift" class="p-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Shift Name *</label>
                        <input type="text" wire:model="shift_name" required placeholder="e.g. Split Shift" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Code (Max 10 chars) *</label>
                        <input type="text" wire:model="shift_code" required placeholder="e.g. SPLIT" class="w-full px-2 py-1 bg-white border border-slate-300 rounded uppercase font-mono">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Start Time *</label>
                            <input type="time" wire:model="start_time" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">End Time *</label>
                            <input type="time" wire:model="end_time" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Duration (Hours) *</label>
                        <input type="number" step="0.5" wire:model="duration_hours" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showShiftModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            Create Shift
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
