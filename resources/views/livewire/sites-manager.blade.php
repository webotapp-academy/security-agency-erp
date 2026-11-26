<div class="space-y-3.5">

    <!-- TOP CONTROLS & FILTER BAR -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5">
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative w-56">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search site, address, contact..."
                       class="w-full pl-8 pr-2.5 py-1 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select wire:model.live="clientFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="">All Clients</option>
                @foreach ($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->client_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="typeFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white">
                <option value="">All Site Types</option>
                <option value="Residential apartment">Residential Apartment</option>
                <option value="Office">Office / IT Park</option>
                <option value="Warehouse">Warehouse / Depot</option>
                <option value="Factory">Factory / Plant</option>
                <option value="Shopping mall">Shopping Mall</option>
                <option value="Hospital">Hospital / Clinic</option>
                <option value="School">School / College</option>
                <option value="Construction site">Construction Site</option>
                <option value="Other">Other</option>
            </select>
        </div>

        @if(!auth()->user()->isSupervisor())
            <button wire:click="openCreateModal" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white flex items-center space-x-1.5 shadow-xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Deployment Site</span>
            </button>
        @endif
    </div>

    <!-- SITES TABLE -->
    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th>Site Name & Code</th>
                        <th>Client</th>
                        <th>Type & Location</th>
                        <th>Supervisor</th>
                        <th>Required</th>
                        <th>Deployed</th>
                        <th>Manning Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sites as $st)
                        @php
                            $deployed = $st->activeDeployments->count();
                            $shortage = max(0, $st->required_guards - $deployed);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>
                                <div class="font-bold text-slate-900 text-xs">{{ $st->site_name }}</div>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $st->site_code }}</div>
                            </td>
                            <td>
                                <span class="text-xs text-slate-800 font-medium">{{ $st->client->client_name ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $st->site_type }}
                                </span>
                                <p class="text-[10px] text-slate-500 truncate max-w-xs mt-0.5">{{ $st->address }}</p>
                            </td>
                            <td>
                                <span class="text-xs text-slate-800">{{ $st->supervisor->name ?? 'Unassigned' }}</span>
                            </td>
                            <td class="font-semibold text-slate-700 text-xs">{{ $st->required_guards }}</td>
                            <td class="font-bold text-slate-900 text-xs">{{ $deployed }}</td>
                            <td>
                                @if ($shortage == 0)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Fully Manned
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        -{{ $shortage }} Shortage
                                    </span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <button wire:click="viewDetails({{ $st->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 mr-1 cursor-pointer">
                                    Details
                                </button>
                                @if(!auth()->user()->isSupervisor())
                                    <button wire:click="openEditModal({{ $st->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                                        Edit
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-xs text-slate-500">No sites found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-2 border-t border-slate-200 bg-slate-50/50">
            {{ $sites->links() }}
        </div>
    </div>

    <!-- CREATE / EDIT MODAL -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">
                        {{ $isEditing ? 'Edit Site: ' . $site_code : 'Create Security Deployment Site' }}
                    </h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="save" class="overflow-y-auto p-4 space-y-3 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Site Code</label>
                            <input type="text" wire:model="site_code" readonly class="w-full px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono text-slate-600">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Client *</label>
                            <select wire:model="client_id" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="">Select Client</option>
                                @foreach ($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->client_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Site Name *</label>
                            <input type="text" wire:model="site_name" required placeholder="e.g. Tower A & Main Gate" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Site Type *</label>
                            <select wire:model="site_type" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="Residential apartment">Residential Apartment</option>
                                <option value="Office">Office / IT Park</option>
                                <option value="Warehouse">Warehouse / Logistics Yard</option>
                                <option value="Factory">Factory / Plant</option>
                                <option value="Shopping mall">Shopping Mall</option>
                                <option value="Hospital">Hospital / Healthcare</option>
                                <option value="School">School / Educational</option>
                                <option value="Construction site">Construction Site</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-medium text-slate-700 mb-0.5">Site Physical Address *</label>
                            <textarea wire:model="address" rows="2" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Site Contact Person</label>
                            <input type="text" wire:model="contact_person" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Contact Phone</label>
                            <input type="text" wire:model="contact_phone" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Required Guard Count *</label>
                            <input type="number" min="1" wire:model="required_guards" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Assigned Supervisor</label>
                            <select wire:model="supervisor_id" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="">Select Supervisor</option>
                                @foreach ($supervisors as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ str_replace('_', ' ', $s->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-medium text-slate-700 mb-0.5">Duty Instructions for Guards</label>
                            <textarea wire:model="duty_instructions" rows="2" placeholder="Specific checkpoint rules, visitor logging, weapon safety..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-medium text-slate-700 mb-0.5">Shift Breakdown Requirements</label>
                            <input type="text" wire:model="shift_requirements" placeholder="e.g. 2 Morning, 2 Evening, 1 Night" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            {{ $isEditing ? 'Save Site Changes' : 'Create Deployment Site' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- SITE DETAILS MODAL -->
    @if ($showDetailModal && $selectedSite)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center space-x-2">
                        <span class="font-bold text-xs text-slate-900 uppercase">Site Details: {{ $selectedSite->site_name }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-200 text-slate-800">{{ $selectedSite->site_code }}</span>
                    </div>
                    <button wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <div class="overflow-y-auto p-4 space-y-4 text-xs">
                    <div class="p-3 bg-slate-50 rounded border border-slate-200 grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div>
                            <span class="text-slate-500 block text-[11px]">Client</span>
                            <span class="font-bold text-slate-900">{{ $selectedSite->client->client_name ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[11px]">Required Guards</span>
                            <span class="font-bold text-slate-900">{{ $selectedSite->required_guards }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[11px]">Currently Active</span>
                            <span class="font-bold text-slate-900">{{ $selectedSite->activeDeployments->count() }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[11px]">Supervisor</span>
                            <span class="font-semibold text-slate-800">{{ $selectedSite->supervisor->name ?? 'Unassigned' }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="font-semibold text-slate-700 block mb-0.5">Duty Instructions</span>
                        <p class="p-2.5 rounded bg-slate-50 border border-slate-200 text-slate-700 leading-relaxed">{{ $selectedSite->duty_instructions ?? 'Standard protocol.' }}</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-900 text-xs border-b border-slate-200 pb-1 mb-2">
                            Currently Deployed Guards ({{ $selectedSite->activeDeployments->count() }})
                        </h4>
                        <div class="space-y-1.5">
                            @forelse ($selectedSite->activeDeployments as $dep)
                                <div class="p-2 rounded border border-slate-200 bg-white flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-semibold text-slate-900">{{ $dep->securityGuard->full_name }}</span>
                                        <span class="text-slate-400 font-mono text-[10px]">({{ $dep->securityGuard->guard_code }})</span>
                                        <span class="px-1.5 py-0.2 rounded text-[10px] bg-slate-100 font-medium text-slate-700">{{ $dep->designation }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 text-slate-500 text-[11px]">
                                        <span>Shift: {{ $dep->shift->name ?? 'General' }}</span>
                                        <span>Since: {{ $dep->start_date->format('d M Y') }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-slate-500 text-xs py-2">No guards currently deployed at this site.</p>
                            @endforelse
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
