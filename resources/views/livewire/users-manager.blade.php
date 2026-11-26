<div class="space-y-3.5">
    <div class="bg-white p-3 rounded border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <h3 class="font-bold text-xs text-slate-900">Agency Staff & Role Permissions</h3>
            <p class="text-[11px] text-slate-500">Manage Administrator, HR Manager, Site Supervisor, Accountant & Operations Manager accounts.</p>
        </div>
        <button wire:click="openCreateModal" class="px-3 py-1.5 rounded text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white cursor-pointer">
            + Add Staff User
        </button>
    </div>

    <div class="bg-white border border-slate-200 rounded shadow-xs overflow-hidden">
        <table class="w-full text-left compact-table border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                    <th>Staff Name</th>
                    <th>Email & Phone</th>
                    <th>Role</th>
                    <th>Supervised Sites</th>
                    <th>Status</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $u)
                    <tr class="hover:bg-slate-50/80">
                        <td class="font-bold text-slate-900 text-xs">{{ $u->name }}</td>
                        <td>
                            <div class="text-xs text-slate-800">{{ $u->email }}</div>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $u->phone ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 capitalize">
                                {{ str_replace('_', ' ', $u->role) }}
                            </span>
                        </td>
                        <td class="text-xs text-slate-600">{{ $u->supervised_sites_count }} Sites</td>
                        <td>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $u->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $u->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <button wire:click="openEditModal({{ $u->id }})" class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 cursor-pointer">
                                Edit
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg border border-slate-200 max-w-md w-full overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-xs text-slate-900 uppercase">{{ $isEditing ? 'Edit Staff User' : 'Create Staff User' }}</h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
                </div>
                <form wire:submit="saveUser" class="p-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Full Name *</label>
                        <input type="text" wire:model="name" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Email Address *</label>
                        <input type="email" wire:model="email" required class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        @error('email') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Phone</label>
                            <input type="text" wire:model="phone" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-700 mb-0.5">Role *</label>
                            <select wire:model="role" class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                                <option value="admin">Administrator</option>
                                <option value="hr_manager">HR Manager</option>
                                <option value="site_supervisor">Site Supervisor</option>
                                <option value="accountant">Accountant</option>
                                <option value="operations_manager">Operations Manager</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">{{ $isEditing ? 'New Password (leave blank to keep current)' : 'Password *' }}</label>
                        <input type="password" wire:model="password" {{ $isEditing ? '' : 'required' }} class="w-full px-2 py-1 bg-white border border-slate-300 rounded">
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" wire:model="is_active" id="user_active" class="rounded border-slate-300">
                        <label for="user_active" class="font-medium text-slate-700">Account Active</label>
                    </div>
                    <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showModal', false)" class="px-3 py-1.5 rounded border border-slate-300 text-slate-700">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 text-white font-semibold">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
