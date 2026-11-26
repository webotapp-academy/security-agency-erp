<div class="space-y-3.5">

    <!-- TOP CONTROLS & FILTER BAR -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5">
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative w-56">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search guard or site..."
                       class="w-full pl-8 pr-2.5 py-1 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select wire:model.live="siteFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="">All Deployment Sites</option>
                @foreach ($sites as $s)
                    <option value="{{ $s->id }}">{{ $s->site_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="active">Active Deployments Only</option>
                <option value="relieved">Relieved Duty History</option>
                <option value="transferred">Transfers</option>
                <option value="">All History</option>
            </select>
        </div>

        <button wire:click="openAssignModal" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white flex items-center space-x-1.5 shadow-xs cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Assign Guard to Site</span>
        </button>
    </div>

    <!-- SITES MANNING MINI BANNER -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
        @foreach ($sites->take(4) as $st)
            @php
                $depCount = $st->activeDeployments->count();
                $diff = $st->required_guards - $depCount;
            @endphp
            <div class="bg-white p-2.5 rounded border border-slate-200 text-xs shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-800 truncate pr-1">{{ $st->site_name }}</span>
                    @if ($diff <= 0)
                        <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0" title="Fully Manned"></span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0" title="Shortage"></span>
                    @endif
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex justify-between">
                    <span>Manning: <strong class="text-slate-800">{{ $depCount }} / {{ $st->required_guards }}</strong></span>
                    @if($diff > 0)
                        <span class="text-rose-600 font-semibold">-{{ $diff }} guard</span>
                    @else
                        <span class="text-emerald-600 font-semibold">Ready</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- DEPLOYMENTS TABLE -->
    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th>Guard Name & Code</th>
                        <th>Assigned Site</th>
                        <th>Client</th>
                        <th>Shift</th>
                        <th>Designation</th>
                        <th>Deployment Period</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($deployments as $dep)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>
                                <div class="font-bold text-slate-900 text-xs">{{ $dep->securityGuard->full_name }}</div>
                                <span class="text-[10px] font-mono text-slate-400 font-normal">{{ $dep->securityGuard->guard_code }}</span>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-800 text-xs">{{ $dep->site->site_name }}</div>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $dep->site->site_code }}</span>
                            </td>
                            <td>
                                <span class="text-xs text-slate-700">{{ $dep->site->client->client_name ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                    {{ $dep->shift->name ?? 'General' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-xs text-slate-800">{{ $dep->designation }}</span>
                            </td>
                            <td>
                                <div class="text-xs text-slate-800 font-medium">
                                    {{ $dep->start_date->format('d M Y') }}
                                    @if ($dep->end_date)
                                        <span class="text-slate-400 font-normal">to {{ $dep->end_date->format('d M Y') }}</span>
                                    @else
                                        <span class="text-emerald-600 font-semibold text-[10px]">&bull; Present</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if ($dep->status === 'active')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Active
                                    </span>
                                @elseif ($dep->status === 'transferred')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        Transferred
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        Relieved
                                    </span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                @if ($dep->status === 'active')
                                    <button wire:click="openTransferModal({{ $dep->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 mr-1 cursor-pointer">
                                        Transfer
                                    </button>
                                    <button wire:click="openRelieveModal({{ $dep->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 cursor-pointer">
                                        Relieve
                                    </button>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Closed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-xs text-slate-500">No deployments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-2 border-t border-slate-200 bg-slate-50/50">
            {{ $deployments->links() }}
        </div>
    </div>

    <!-- ASSIGN GUARD MODAL -->
    @if ($showAssignModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">Deploy Guard to Site</h3>
                    <button wire:click="$set('showAssignModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="assignGuard" class="overflow-y-auto p-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Select Guard to Deploy *</label>
                        <select wire:model="guard_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="">Select an available active guard</option>
                            @foreach ($availableGuards as $g)
                                <option value="{{ $g->id }}">{{ $g->full_name }} ({{ $g->guard_code }} - {{ $g->designation }})</option>
                            @endforeach
                        </select>
                        @error('guard_id') <span class="text-rose-600 text-[10px] block mt-0.5">{{ $message }}</span> @enderror
                        @if($availableGuards->isEmpty())
                            <span class="text-[10px] text-amber-600 block mt-0.5">All active guards are currently deployed or registered as resigned/inactive.</span>
                        @endif
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Deployment Target Site *</label>
                        <select wire:model="site_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="">Select Site</option>
                            @foreach ($sites as $s)
                                <option value="{{ $s->id }}">{{ $s->site_name }} ({{ $s->client->client_name ?? '' }}) - Req: {{ $s->required_guards }}</option>
                            @endforeach
                        </select>
                        @error('site_id') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Shift Assignment</label>
                            <select wire:model="shift_id" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="">General</option>
                                @foreach ($shifts as $sh)
                                    <option value="{{ $sh->id }}">{{ $sh->name }} ({{ $sh->formatted_time }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Designation at Site</label>
                            <input type="text" wire:model="designation" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Deployment Start Date *</label>
                        <input type="date" wire:model="start_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Specific Site Duty Instructions</label>
                        <textarea wire:model="duty_instructions" rows="2" placeholder="Post orders, checkpoints, radio channel..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showAssignModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            Confirm Deployment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- TRANSFER MODAL -->
    @if ($showTransferModal && $selectedDeployment)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-md w-full overflow-hidden text-xs">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-slate-900 uppercase">Transfer Guard to New Site</h3>
                    <button wire:click="$set('showTransferModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="transferGuard" class="p-4 space-y-3">
                    <div class="p-2.5 bg-slate-50 rounded border border-slate-200">
                        <p class="font-bold text-slate-900">{{ $selectedDeployment->securityGuard->full_name }}</p>
                        <p class="text-slate-500">Currently at: <span class="font-semibold text-slate-800">{{ $selectedDeployment->site->site_name }}</span></p>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Transfer To New Site *</label>
                        <select wire:model="transfer_site_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            <option value="">Select Target Site</option>
                            @foreach ($sites as $s)
                                @if($s->id !== $selectedDeployment->site_id)
                                    <option value="{{ $s->id }}">{{ $s->site_name }} ({{ $s->client->client_name ?? '' }})</option>
                                @endif
                            @endforeach
                        </select>
                        @error('transfer_site_id') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">New Shift</label>
                            <select wire:model="transfer_shift_id" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="">General</option>
                                @foreach ($shifts as $sh)
                                    <option value="{{ $sh->id }}">{{ $sh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Effective Date *</label>
                            <input type="date" wire:model="transfer_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showTransferModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 text-white font-semibold">Execute Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- RELIEVE MODAL -->
    @if ($showRelieveModal && $selectedDeployment)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-md w-full overflow-hidden text-xs">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-slate-900 uppercase">Relieve Guard from Site</h3>
                    <button wire:click="$set('showRelieveModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="relieveGuard" class="p-4 space-y-3">
                    <p class="text-slate-600">
                        You are relieving <strong class="text-slate-900">{{ $selectedDeployment->securityGuard->full_name }}</strong> from <strong class="text-slate-900">{{ $selectedDeployment->site->site_name }}</strong>.
                        The guard will return to the available roster.
                    </p>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Relieve Effective Date *</label>
                        <input type="date" wire:model="relieve_date" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Reason for Relieving</label>
                        <input type="text" wire:model="relieve_reason" required placeholder="e.g. End of contract / Rotation / Client request" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>

                    <div class="pt-2 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showRelieveModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-rose-600 hover:bg-rose-700 text-white font-semibold">Confirm Relieve</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
