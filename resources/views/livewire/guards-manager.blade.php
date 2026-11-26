<div class="space-y-3.5">

    <!-- TOP CONTROLS & FILTER BAR -->
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-2.5">
        <div class="flex flex-wrap items-center gap-2">
            <!-- Search input -->
            <div class="relative w-56">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, ID, phone..."
                       class="w-full pl-8 pr-2.5 py-1 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Status Filter -->
            <select wire:model.live="statusFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
                <option value="resigned">Resigned</option>
                <option value="terminated">Terminated</option>
            </select>

            <!-- Designation Filter -->
            <select wire:model.live="designationFilter" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900">
                <option value="">All Designations</option>
                @foreach ($designations as $des)
                    <option value="{{ $des }}">{{ $des }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <button wire:click="openCreateModal" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white flex items-center space-x-1.5 shadow-xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Register New Guard</span>
            </button>
        </div>
    </div>

    <!-- GUARDS COMPACT TABLE -->
    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left compact-table border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th>Guard ID & Name</th>
                        <th>Designation</th>
                        <th>Contact</th>
                        <th>Police Verif.</th>
                        <th>Current Deployment</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($guards as $guard)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td>
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs flex-shrink-0">
                                        {{ strtoupper(substr($guard->full_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs flex items-center space-x-1.5">
                                            <span>{{ $guard->full_name }}</span>
                                            <span class="text-[10px] font-mono text-slate-400 font-normal">({{ $guard->guard_code }})</span>
                                        </div>
                                        <div class="text-[10px] text-slate-500">
                                            Joined {{ $guard->joining_date ? $guard->joining_date->format('d M Y') : '—' }} &bull; {{ $guard->experience_years ?? 'Exp N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium text-slate-800 text-xs">{{ $guard->designation }}</span>
                                <span class="block text-[10px] text-slate-500 capitalize">{{ str_replace('_', ' ', $guard->employment_type) }}</span>
                            </td>
                            <td>
                                <div class="text-xs text-slate-800 font-mono">{{ $guard->phone }}</div>
                                @if($guard->alternate_phone)
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $guard->alternate_phone }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($guard->police_verification_status === 'verified')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Verified
                                    </span>
                                @elseif ($guard->police_verification_status === 'pending')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($guard->activeDeployment)
                                    <div class="text-xs font-semibold text-slate-900 leading-tight">
                                        {{ $guard->activeDeployment->site->site_name ?? 'Active Site' }}
                                    </div>
                                    <span class="text-[10px] text-blue-600 font-medium">Deployed</span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                        Available / Unassigned
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'inactive' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        'suspended' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'resigned' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'terminated' => 'bg-rose-100 text-rose-800 border-rose-300',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold border capitalize {{ $statusClasses[$guard->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $guard->status }}
                                </span>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <button wire:click="viewDetails({{ $guard->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 mr-1 cursor-pointer">
                                    View
                                </button>
                                <button wire:click="openEditModal({{ $guard->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-xs text-slate-500">
                                No guards found matching your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-2 border-t border-slate-200 bg-slate-50/50">
            {{ $guards->links() }}
        </div>
    </div>

    <!-- CREATE / EDIT GUARD MODAL -->
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">
                        {{ $isEditing ? 'Edit Guard: ' . $guard_code : 'Register New Security Guard' }}
                    </h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <form wire:submit="save" class="overflow-y-auto p-4 space-y-4 text-xs">
                    
                    <!-- Section 1: Basic Info -->
                    <div>
                        <h4 class="font-semibold text-slate-900 border-b border-slate-200 pb-1 mb-2">1. Personal & Identity Details</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Guard Code</label>
                                <input type="text" wire:model="guard_code" readonly class="w-full px-2 py-1 bg-slate-100 border border-slate-300 rounded font-mono text-slate-600">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Full Name *</label>
                                <input type="text" wire:model="full_name" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                @error('full_name') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Father's Name</label>
                                <input type="text" wire:model="father_name" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Date of Birth</label>
                                <input type="date" wire:model="dob" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Gender</label>
                                <select wire:model="gender" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Blood Group</label>
                                <input type="text" wire:model="blood_group" placeholder="e.g. B+" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Mobile Phone *</label>
                                <input type="text" wire:model="phone" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                @error('phone') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Alternate Mobile</label>
                                <input type="text" wire:model="alternate_phone" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Email Address</label>
                                <input type="email" wire:model="email" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-2.5">
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Current Address</label>
                                <textarea wire:model="current_address" rows="2" class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Permanent Address</label>
                                <textarea wire:model="permanent_address" rows="2" class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-2">
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Emergency Contact Person</label>
                                <input type="text" wire:model="emergency_contact_name" placeholder="Name (Relationship)" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Emergency Contact Phone</label>
                                <input type="text" wire:model="emergency_contact_phone" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Verification & Employment -->
                    <div>
                        <h4 class="font-semibold text-slate-900 border-b border-slate-200 pb-1 mb-2">2. Verification, Qualifications & Employment</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Aadhaar Card Reference</label>
                                <input type="text" wire:model="aadhaar_reference" placeholder="12 digit Aadhaar" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">PAN Card Number</label>
                                <input type="text" wire:model="pan_number" placeholder="PAN Number" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono uppercase">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Police Verification</label>
                                <select wire:model="police_verification_status" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                    <option value="pending">Pending</option>
                                    <option value="verified">Verified</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Police Ref. / Thana File No.</label>
                                <input type="text" wire:model="police_verification_ref" placeholder="PV/xxx/2026" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Background Check</label>
                                <select wire:model="background_verification_status" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                    <option value="pending">Pending</option>
                                    <option value="verified">Verified</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Joining Date</label>
                                <input type="date" wire:model="joining_date" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Designation *</label>
                                <select wire:model="designation" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                    <option value="Security Guard">Security Guard</option>
                                    <option value="Head Guard">Head Guard</option>
                                    <option value="Armed Gunman">Armed Gunman</option>
                                    <option value="Lady Guard">Lady Guard</option>
                                    <option value="Site Supervisor">Site Supervisor</option>
                                    <option value="Field Officer">Field Officer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Employment Type</label>
                                <select wire:model="employment_type" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                    <option value="full_time">Full Time</option>
                                    <option value="part_time">Part Time</option>
                                    <option value="contract">Contract</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Guard Status *</label>
                                <select wire:model="status" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="resigned">Resigned</option>
                                    <option value="terminated">Terminated</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Qualification</label>
                                <input type="text" wire:model="qualification" placeholder="e.g. 10th / 12th / BA" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Experience</label>
                                <input type="text" wire:model="experience_years" placeholder="e.g. 3 Years" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block font-medium text-slate-700 mb-0.5">Security Training Details</label>
                                <textarea wire:model="training_details" rows="2" placeholder="PSARA certification, weapon license, fire safety training..." class="w-full px-2 py-1 bg-white border border-slate-300 rounded"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Bank & Compensation -->
                    <div>
                        <h4 class="font-semibold text-slate-900 border-b border-slate-200 pb-1 mb-2">3. Compensation & Bank Account (Confidential)</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Salary Type</label>
                                <select wire:model="salary_type" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                    <option value="monthly">Monthly</option>
                                    <option value="daily">Daily</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Basic Salary (₹) *</label>
                                <input type="number" step="0.01" wire:model="basic_salary" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Allowances (₹)</label>
                                <input type="number" step="0.01" wire:model="allowances" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Overtime Rate (₹/hr)</label>
                                <input type="number" step="0.01" wire:model="overtime_rate" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Bank Name</label>
                                <input type="text" wire:model="bank_name" placeholder="SBI / HDFC / PNB" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Account Holder Name</label>
                                <input type="text" wire:model="account_holder_name" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">Account Number</label>
                                <input type="text" wire:model="account_number" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-700 mb-0.5">IFSC Code</label>
                                <input type="text" wire:model="ifsc_code" placeholder="e.g. SBIN0001234" class="w-full px-2 py-1 bg-white border border-slate-300 rounded font-mono uppercase">
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions Footer -->
                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700 hover:bg-slate-100 font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">
                            {{ $isEditing ? 'Save Changes' : 'Register Guard' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- DETAIL / VIEW GUARD MODAL -->
    @if ($showDetailModal && $selectedGuard)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center space-x-2">
                        <span class="font-bold text-xs text-slate-900 uppercase">Guard Profile: {{ $selectedGuard->full_name }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-200 text-slate-800">{{ $selectedGuard->guard_code }}</span>
                    </div>
                    <button wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>

                <div class="overflow-y-auto p-4 space-y-4 text-xs">
                    <!-- Basic info header card -->
                    <div class="p-3 bg-slate-50 rounded border border-slate-200 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-slate-900 text-amber-400 font-bold flex items-center justify-center text-sm">
                                {{ strtoupper(substr($selectedGuard->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $selectedGuard->full_name }}</h4>
                                <p class="text-[11px] text-slate-500">{{ $selectedGuard->designation }} &bull; {{ $selectedGuard->blood_group ? 'Blood: ' . $selectedGuard->blood_group : '' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800 capitalize">{{ $selectedGuard->status }}</span>
                            @if($selectedGuard->activeDeployment)
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">Deployed at {{ $selectedGuard->activeDeployment->site->site_name ?? 'Site' }}</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-200 text-slate-700">Available</span>
                            @endif
                        </div>
                    </div>

                    <!-- 3 Column details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="p-3 border border-slate-200 rounded space-y-1.5">
                            <span class="font-bold text-slate-800 block text-[11px] border-b pb-1">Personal Info</span>
                            <div><span class="text-slate-500">Father:</span> <span class="font-medium text-slate-800">{{ $selectedGuard->father_name ?? '—' }}</span></div>
                            <div><span class="text-slate-500">DOB:</span> <span class="font-medium text-slate-800">{{ $selectedGuard->dob ? $selectedGuard->dob->format('d M Y') : '—' }}</span></div>
                            <div><span class="text-slate-500">Phone:</span> <span class="font-medium text-slate-800 font-mono">{{ $selectedGuard->phone }}</span></div>
                            <div><span class="text-slate-500">Alt Phone:</span> <span class="font-medium text-slate-800 font-mono">{{ $selectedGuard->alternate_phone ?? '—' }}</span></div>
                            <div><span class="text-slate-500">Address:</span> <p class="text-slate-700 text-[11px] mt-0.5">{{ $selectedGuard->current_address ?? '—' }}</p></div>
                        </div>

                        <div class="p-3 border border-slate-200 rounded space-y-1.5">
                            <span class="font-bold text-slate-800 block text-[11px] border-b pb-1">Verifications</span>
                            <div><span class="text-slate-500">Aadhaar (Masked):</span> <span class="font-medium text-slate-800 font-mono">{{ $selectedGuard->masked_aadhaar ?? '—' }}</span></div>
                            <div><span class="text-slate-500">PAN:</span> <span class="font-medium text-slate-800 font-mono">{{ $selectedGuard->pan_number ?? '—' }}</span></div>
                            <div><span class="text-slate-500">Police Check:</span> <span class="font-semibold capitalize text-emerald-700">{{ $selectedGuard->police_verification_status }}</span></div>
                            <div><span class="text-slate-500">Ref:</span> <span class="text-slate-700 font-mono text-[11px]">{{ $selectedGuard->police_verification_ref ?? '—' }}</span></div>
                            <div><span class="text-slate-500">Training:</span> <p class="text-slate-700 text-[11px] mt-0.5">{{ $selectedGuard->training_details ?? 'Standard Induction' }}</p></div>
                        </div>

                        <div class="p-3 border border-slate-200 rounded space-y-1.5">
                            <span class="font-bold text-slate-800 block text-[11px] border-b pb-1">Salary & Bank</span>
                            <div><span class="text-slate-500">Monthly Basic:</span> <span class="font-bold text-slate-900">₹{{ number_format($selectedGuard->basic_salary) }}</span></div>
                            <div><span class="text-slate-500">Allowances:</span> <span class="font-medium text-slate-800">₹{{ number_format($selectedGuard->allowances) }}</span></div>
                            <div><span class="text-slate-500">OT Rate:</span> <span class="font-medium text-slate-800">₹{{ number_format($selectedGuard->overtime_rate) }}/hr</span></div>
                            @if(auth()->user()->isAdmin() || auth()->user()->isAccountant())
                                <div class="pt-1 border-t text-[11px]">
                                    <div><span class="text-slate-500">Bank:</span> <span class="font-medium">{{ $selectedGuard->bank_name ?? '—' }}</span></div>
                                    <div><span class="text-slate-500">A/C:</span> <span class="font-mono">{{ $selectedGuard->account_number ?? '—' }}</span></div>
                                    <div><span class="text-slate-500">IFSC:</span> <span class="font-mono">{{ $selectedGuard->ifsc_code ?? '—' }}</span></div>
                                </div>
                            @else
                                <div class="text-[10px] text-slate-400 italic pt-1">Bank details restricted.</div>
                            @endif
                        </div>
                    </div>

                    <!-- Documents Attached Section -->
                    <div class="p-3 border border-slate-200 rounded">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <span class="font-bold text-xs text-slate-900">Uploaded Documents ({{ $selectedGuard->documents->count() }})</span>
                        </div>

                        @if (session('doc_success'))
                            <div class="my-2 p-1.5 bg-emerald-50 text-emerald-700 text-[11px] rounded">{{ session('doc_success') }}</div>
                        @endif

                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @forelse ($selectedGuard->documents as $doc)
                                <div class="p-2 bg-slate-50 rounded border border-slate-200 flex items-center justify-between">
                                    <div class="truncate pr-2">
                                        <p class="font-semibold text-slate-800 truncate">{{ $doc->document_title }}</p>
                                        <p class="text-[10px] text-slate-500 capitalize">{{ str_replace('_', ' ', $doc->document_type) }} &bull; {{ $doc->document_number ?? 'Ref N/A' }}</p>
                                    </div>
                                    <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">On File</span>
                                </div>
                            @empty
                                <p class="text-slate-500 text-xs py-2">No documents attached yet.</p>
                            @endforelse
                        </div>

                        <!-- Add document form -->
                        <div class="mt-3 pt-2 border-t border-slate-200">
                            <span class="font-semibold text-[11px] text-slate-700 block mb-1">Attach New Document Record</span>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                                <select wire:model="doc_type" class="px-2 py-1 bg-white border border-slate-300 rounded text-xs">
                                    <option value="aadhaar">Aadhaar Card</option>
                                    <option value="pan">PAN Card</option>
                                    <option value="police_verification">Police Verification</option>
                                    <option value="training_certificate">Training Certificate</option>
                                    <option value="appointment_letter">Appointment Letter</option>
                                    <option value="photograph">Photograph</option>
                                    <option value="other">Other Document</option>
                                </select>
                                <input type="text" wire:model="doc_title" placeholder="Document Title *" class="px-2 py-1 bg-white border border-slate-300 rounded text-xs">
                                <input type="text" wire:model="doc_number" placeholder="Document Ref No." class="px-2 py-1 bg-white border border-slate-300 rounded text-xs">
                                <button type="button" wire:click="addDocument" class="px-3 py-1 bg-slate-900 hover:bg-slate-800 text-white rounded text-xs font-semibold">
                                    Attach Document
                                </button>
                            </div>
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
