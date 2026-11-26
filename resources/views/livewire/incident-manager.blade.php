<div class="space-y-3.5">

    <!-- TOP CONTROLS & FILTER BAR -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5">
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative w-56">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search incident code, details..."
                       class="w-full pl-8 pr-2.5 py-1 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Category Filter -->
            <select wire:model.live="categoryFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="">All Categories</option>
                <option value="theft">Theft / Larceny</option>
                <option value="unauthorized_entry">Unauthorized Entry</option>
                <option value="fire">Fire / Smoke</option>
                <option value="property_damage">Property Damage</option>
                <option value="safety_incident">Safety Hazard</option>
                <option value="visitor_dispute">Visitor Dispute</option>
                <option value="guard_misconduct">Guard Misconduct</option>
                <option value="other">Other Incident</option>
            </select>

            <!-- Status Filter -->
            <select wire:model.live="statusFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="">All Resolutions</option>
                <option value="open">Open</option>
                <option value="investigating">Under Investigation</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
        </div>

        <button wire:click="openCreateModal" class="px-3 py-1.5 rounded text-xs font-semibold bg-rose-700 hover:bg-rose-800 text-white flex items-center space-x-1.5 shadow-xs cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Log Security Incident</span>
        </button>
    </div>

    <!-- INCIDENTS TABLE -->
    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th>Incident Code & Date</th>
                        <th>Category</th>
                        <th>Site & Client</th>
                        <th>Reported By</th>
                        <th>Description / Summary</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($incidents as $inc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>
                                <div class="font-bold text-slate-900 text-xs font-mono">{{ $inc->incident_code }}</div>
                                <span class="text-[10px] text-slate-500">{{ $inc->incident_datetime->format('d M Y, h:i A') }}</span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-100 text-slate-800 capitalize">
                                    {{ str_replace('_', ' ', $inc->category) }}
                                </span>
                                @if ($inc->confidential)
                                    <span class="inline-block ml-1 px-1 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800">Confidential</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-slate-900 text-xs">{{ $inc->site->site_name ?? '—' }}</div>
                                <span class="text-[10px] text-slate-400">{{ $inc->client->client_name ?? '' }}</span>
                            </td>
                            <td>
                                <span class="font-medium text-slate-800 text-xs">{{ $inc->reported_by }}</span>
                            </td>
                            <td>
                                <p class="text-xs text-slate-700 max-w-sm truncate" title="{{ $inc->description }}">{{ $inc->description }}</p>
                                @if ($inc->action_taken)
                                    <p class="text-[10px] text-emerald-700 font-medium truncate mt-0.5">Action: {{ $inc->action_taken }}</p>
                                @endif
                            </td>
                            <td>
                                @php
                                    $resStyles = [
                                        'open' => 'bg-rose-50 text-rose-700 border-rose-200 font-bold',
                                        'investigating' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'closed' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border capitalize {{ $resStyles[$inc->resolution_status] ?? '' }}">
                                    {{ $inc->resolution_status }}
                                </span>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <button wire:click="viewDetails({{ $inc->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                                    Investigation / Action
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-xs text-slate-500">No incident reports recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-2 border-t border-slate-200 bg-slate-50/50">
            {{ $incidents->links() }}
        </div>
    </div>

    <!-- LOG INCIDENT MODAL -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-lg w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Log Security Incident / Breach</h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="saveIncident" class="p-4 space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Incident Reference</label>
                            <input type="text" wire:model="incident_code" readonly class="w-full px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono font-semibold">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Date & Time *</label>
                            <input type="datetime-local" wire:model="incident_datetime" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Deployment Site *</label>
                            <select wire:model.live="site_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="">-- Choose Site --</option>
                                @foreach ($sites as $s)
                                    <option value="{{ $s->id }}">{{ $s->site_name }} ({{ $s->client->client_name ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Category *</label>
                            <select wire:model="category" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="theft">Theft / Larceny</option>
                                <option value="unauthorized_entry">Unauthorized Entry / Trespass</option>
                                <option value="fire">Fire / Smoke Emergency</option>
                                <option value="property_damage">Property Damage</option>
                                <option value="safety_incident">Safety Incident</option>
                                <option value="visitor_dispute">Visitor / Resident Dispute</option>
                                <option value="guard_misconduct">Guard Misconduct / Sleeping on Duty</option>
                                <option value="other">Other Incident</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Reported By *</label>
                            <input type="text" wire:model="reported_by" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Involved Guards</label>
                            <input type="text" wire:model="involved_guards" placeholder="e.g. SEC-G-002, SEC-G-005" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Incident Description *</label>
                        <textarea wire:model="description" rows="3" required placeholder="What happened, chronology of events, damages or findings..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Immediate Action Taken</label>
                        <textarea wire:model="action_taken" rows="2" placeholder="Action taken by on-duty guard, supervisor arrival, police notification..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                    </div>

                    <div class="flex items-center space-x-2 pt-1">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="confidential" class="rounded border-slate-300 text-slate-900 text-xs">
                            <span class="ml-1.5 font-medium text-slate-700">Mark as Confidential Record (Restricted to Admin/Ops)</span>
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-rose-700 hover:bg-rose-800 text-white font-semibold">
                            Log Incident
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- INVESTIGATION & ACTION MODAL -->
    @if ($showDetailModal && $selectedIncident)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-lg w-full flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <span class="font-bold text-xs text-slate-900 uppercase">Incident Dossier: {{ $selectedIncident->incident_code }}</span>
                    <button wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <div class="p-4 space-y-3 text-xs overflow-y-auto">
                    <div class="p-2.5 bg-slate-50 rounded border border-slate-200 grid grid-cols-2 gap-2 text-[11px]">
                        <div>
                            <div><span class="text-slate-500">Site:</span> <span class="font-bold text-slate-800">{{ $selectedIncident->site->site_name }}</span></div>
                            <div><span class="text-slate-500">Client:</span> <span>{{ $selectedIncident->client->client_name ?? '—' }}</span></div>
                            <div><span class="text-slate-500">Reported By:</span> <span>{{ $selectedIncident->reported_by }}</span></div>
                        </div>
                        <div>
                            <div><span class="text-slate-500">Datetime:</span> <span>{{ $selectedIncident->incident_datetime->format('d M Y, h:i A') }}</span></div>
                            <div><span class="text-slate-500">Category:</span> <span class="font-bold uppercase">{{ str_replace('_', ' ', $selectedIncident->category) }}</span></div>
                            <div><span class="text-slate-500">Involved Guards:</span> <span>{{ $selectedIncident->involved_guards ?? 'None specified' }}</span></div>
                        </div>
                    </div>

                    <div>
                        <span class="font-bold text-slate-800 block text-[11px] mb-0.5">Incident Report:</span>
                        <div class="p-2.5 bg-white border border-slate-200 rounded text-slate-700 whitespace-pre-line leading-relaxed">
                            {{ $selectedIncident->description }}
                        </div>
                    </div>

                    <!-- Update Resolution Form -->
                    <div class="p-3 bg-amber-50/50 border border-amber-200 rounded space-y-2">
                        <span class="font-bold text-amber-900 block text-[11px]">Update Investigation & Resolution Status</span>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Resolution Status</label>
                            <select wire:model="update_status" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-semibold text-xs">
                                <option value="open">Open (Active Concern)</option>
                                <option value="investigating">Under Investigation</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed / Solved</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Actions Taken / Resolution Notes</label>
                            <textarea wire:model="update_action_taken" rows="2" class="w-full px-2 py-1 bg-white border border-slate-300 rounded text-xs"></textarea>
                        </div>
                        <button type="button" wire:click="updateResolution" class="px-3 py-1 bg-slate-900 hover:bg-slate-800 text-white rounded font-semibold text-xs">
                            Save Incident Update
                        </button>
                    </div>
                </div>

                <div class="px-4 py-2 border-t border-slate-200 bg-slate-50 flex justify-end">
                    <button wire:click="$set('showDetailModal', false)" class="px-3 py-1 bg-slate-200 hover:bg-slate-300 rounded text-xs font-medium text-slate-700">Close</button>
                </div>
            </div>
        </div>
    @endif

</div>
