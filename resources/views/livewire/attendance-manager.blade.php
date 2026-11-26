<div class="space-y-3.5">

    <!-- TOP CONTROLS & DATE/SITE SELECTOR -->
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
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Bulk Action -->
            <div class="flex items-center space-x-1">
                <select wire:model="bulkStatus" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded">
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="weekly_off">Weekly Off</option>
                    <option value="holiday">Holiday</option>
                </select>
                <button wire:click="bulkMarkAll" class="px-2.5 py-1 text-xs font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 rounded border border-slate-300 cursor-pointer">
                    Apply to All
                </button>
            </div>

            <button wire:click="populateDeployedGuards" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white flex items-center space-x-1.5 shadow-xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Sync Deployed Guards</span>
            </button>
        </div>
    </div>

    <!-- SUMMARY BAR -->
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
        <div class="p-2 bg-white border border-slate-200 rounded text-center">
            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Present</span>
            <span class="text-base font-bold text-emerald-700">{{ $summary['present'] }}</span>
        </div>
        <div class="p-2 bg-white border border-slate-200 rounded text-center">
            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Absent</span>
            <span class="text-base font-bold text-rose-700">{{ $summary['absent'] }}</span>
        </div>
        <div class="p-2 bg-white border border-slate-200 rounded text-center">
            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Half Day</span>
            <span class="text-base font-bold text-amber-700">{{ $summary['half_day'] }}</span>
        </div>
        <div class="p-2 bg-white border border-slate-200 rounded text-center">
            <span class="text-[10px] text-slate-500 uppercase font-semibold block">On Leave</span>
            <span class="text-base font-bold text-blue-700">{{ $summary['leave'] }}</span>
        </div>
        <div class="p-2 bg-white border border-slate-200 rounded text-center">
            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Overtime</span>
            <span class="text-base font-bold text-purple-700">{{ $summary['overtime'] }}</span>
        </div>
        <div class="p-2 bg-white border border-slate-200 rounded text-center">
            <span class="text-[10px] text-slate-500 uppercase font-semibold block">Weekly Off</span>
            <span class="text-base font-bold text-slate-700">{{ $summary['weekly_off'] }}</span>
        </div>
    </div>

    <!-- ATTENDANCE SHEET TABLE -->
    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <div class="px-4 py-2.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <span class="font-bold text-xs text-slate-900">
                Attendance Roll for {{ \Carbon\Carbon::parse($selectedDate)->format('D, d M Y') }}
            </span>
            <span class="text-[11px] text-slate-500 font-medium">{{ $attendances->count() }} Guards on Roll</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th>Guard ID & Name</th>
                        <th>Site & Shift</th>
                        <th>Timing (In / Out)</th>
                        <th>Late / Overtime</th>
                        <th>Current Status</th>
                        <th>Quick Status Mark</th>
                        <th class="text-right">Action / Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($attendances as $att)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>
                                <div class="font-bold text-slate-900 text-xs">{{ $att->securityGuard->full_name ?? '—' }}</div>
                                <span class="text-[10px] font-mono text-slate-400">{{ $att->securityGuard->guard_code ?? '' }}</span>
                            </td>
                            <td>
                                <div class="text-xs text-slate-800 font-medium">{{ $att->site->site_name ?? '—' }}</div>
                                <span class="text-[10px] text-slate-500">{{ $att->shift->name ?? 'General Shift' }}</span>
                            </td>
                            <td>
                                <div class="text-xs font-mono text-slate-800">
                                    {{ $att->check_in_time ? substr($att->check_in_time, 0, 5) : '—' }} - 
                                    {{ $att->check_out_time ? substr($att->check_out_time, 0, 5) : '—' }}
                                </div>
                            </td>
                            <td>
                                <div class="text-xs">
                                    @if ($att->late_minutes > 0)
                                        <span class="text-amber-700 font-semibold">{{ $att->late_minutes }}m Late</span>
                                    @endif
                                    @if ($att->overtime_hours > 0)
                                        <span class="text-purple-700 font-semibold block">+{{ (float)$att->overtime_hours }}h OT</span>
                                    @endif
                                    @if ($att->late_minutes == 0 && $att->overtime_hours == 0)
                                        <span class="text-slate-400 text-[11px]">Normal</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusPill = [
                                        'present' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'absent' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'half_day' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'weekly_off' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'leave' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'holiday' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'overtime' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border capitalize {{ $statusPill[$att->status] ?? '' }}">
                                    {{ str_replace('_', ' ', $att->status) }}
                                </span>
                            </td>
                            <td>
                                <!-- 1-Click Quick Status Switcher -->
                                <div class="inline-flex rounded border border-slate-200 bg-white p-0.5 text-[10px]">
                                    <button wire:click="quickSetStatus({{ $att->id }}, 'present')" title="Present"
                                            class="px-1.5 py-0.5 rounded {{ $att->status === 'present' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">P</button>
                                    <button wire:click="quickSetStatus({{ $att->id }}, 'absent')" title="Absent"
                                            class="px-1.5 py-0.5 rounded {{ $att->status === 'absent' ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">A</button>
                                    <button wire:click="quickSetStatus({{ $att->id }}, 'half_day')" title="Half Day"
                                            class="px-1.5 py-0.5 rounded {{ $att->status === 'half_day' ? 'bg-amber-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">HD</button>
                                    <button wire:click="quickSetStatus({{ $att->id }}, 'weekly_off')" title="Weekly Off"
                                            class="px-1.5 py-0.5 rounded {{ $att->status === 'weekly_off' ? 'bg-slate-700 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">WO</button>
                                </div>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <button wire:click="openEditModal({{ $att->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                                    Edit Details
                                </button>
                                @if ($att->audits->isNotEmpty())
                                    <span title="Has {{ $att->audits->count() }} audit correction(s)" class="text-[10px] font-bold text-amber-600 cursor-help ml-1">
                                        [{{ $att->audits->count() }} edits]
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-xs text-slate-500">
                                No attendance records found for this date. Click "Sync Deployed Guards" to generate the daily roll.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- EDIT ATTENDANCE / AUDIT CORRECTION MODAL -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-md w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Correct Attendance Record</h3>
                    <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="saveEdit" class="p-4 space-y-3 text-xs">
                    <div>
                        <span class="text-slate-500">Guard:</span>
                        <span class="font-bold text-slate-900 ml-1">{{ $guard_name }}</span>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Attendance Status *</label>
                        <select wire:model="status" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="half_day">Half Day</option>
                            <option value="weekly_off">Weekly Off</option>
                            <option value="leave">On Leave</option>
                            <option value="holiday">Holiday</option>
                            <option value="overtime">Overtime Duty</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Check In Time</label>
                            <input type="time" wire:model="check_in_time" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Check Out Time</label>
                            <input type="time" wire:model="check_out_time" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Late Arrival (Minutes)</label>
                            <input type="number" wire:model="late_minutes" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Overtime (Hours)</label>
                            <input type="number" step="0.5" wire:model="overtime_hours" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Remarks / Observation</label>
                        <input type="text" wire:model="remarks" placeholder="Optional notes..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <!-- MANDATORY AUDIT TRAIL REASON -->
                    <div class="p-2.5 bg-amber-50 border border-amber-200 rounded">
                        <label class="block font-bold text-amber-900 mb-0.5">Reason for Correction (Audit Trail) *</label>
                        <input type="text" wire:model="audit_reason" required placeholder="e.g. Corrected swipe error, biometric malfunction, supervisor override..."
                               class="w-full px-2 py-1 bg-white border border-amber-300 rounded text-xs">
                        @error('audit_reason') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showEditModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            Save with Audit Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
