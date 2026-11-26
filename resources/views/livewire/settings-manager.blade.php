<div class="space-y-3.5 max-w-3xl">
    <div class="bg-white p-2 rounded border border-slate-200 shadow-xs inline-flex space-x-1 text-xs font-semibold">
        <button wire:click="$set('activeTab', 'profile')" class="px-3 py-1.5 rounded {{ $activeTab === 'profile' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">My Profile</button>
        <button wire:click="$set('activeTab', 'security')" class="px-3 py-1.5 rounded {{ $activeTab === 'security' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">Security & Password</button>
        @if (auth()->user()->isAdmin())
            <button wire:click="$set('activeTab', 'business')" class="px-3 py-1.5 rounded {{ $activeTab === 'business' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">Business Settings</button>
        @endif
    </div>

    @if ($activeTab === 'profile')
        <div class="bg-white border border-slate-200 rounded shadow-xs p-4">
            <h3 class="font-bold text-xs text-slate-900 uppercase border-b border-slate-200 pb-2 mb-3">My Profile</h3>
            <form wire:submit="saveProfile" class="space-y-3 text-xs max-w-md">
                <div>
                    <label class="block font-medium text-slate-700 mb-0.5">Full Name *</label>
                    <input type="text" wire:model="name" required class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-0.5">Email Address *</label>
                    <input type="email" wire:model="email" required class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded">
                    @error('email') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-0.5">Mobile Number</label>
                    <input type="text" wire:model="phone" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded">
                </div>
                <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">Save Profile</button>
            </form>
        </div>
    @elseif ($activeTab === 'security')
        <div class="bg-white border border-slate-200 rounded shadow-xs p-4">
            <h3 class="font-bold text-xs text-slate-900 uppercase border-b border-slate-200 pb-2 mb-3">Change Password</h3>
            <form wire:submit="changePassword" class="space-y-3 text-xs max-w-md">
                <div>
                    <label class="block font-medium text-slate-700 mb-0.5">Current Password *</label>
                    <input type="password" wire:model="current_password" required class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded">
                    @error('current_password') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-0.5">New Password *</label>
                    <input type="password" wire:model="new_password" required class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded">
                    @error('new_password') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-0.5">Confirm New Password *</label>
                    <input type="password" wire:model="new_password_confirmation" required class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded">
                </div>
                <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">Update Password</button>
            </form>
        </div>
    @elseif ($activeTab === 'business')
        <div class="bg-white border border-slate-200 rounded shadow-xs p-4">
            <h3 class="font-bold text-xs text-slate-900 uppercase border-b border-slate-200 pb-2 mb-3">Agency Business Settings (INR)</h3>
            <form wire:submit="saveBusinessSettings" class="space-y-3 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Software Logo Name *</label>
                        <input type="text" wire:model="software_logo_name" required placeholder="e.g. WeBotApp" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded">
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Agency Name *</label>
                        <input type="text" wire:model="agency_name" required class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded">
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">GSTIN</label>
                        <input type="text" wire:model="agency_gstin" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded font-mono uppercase">
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Contact Phone</label>
                        <input type="text" wire:model="agency_phone" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded">
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Email</label>
                        <input type="email" wire:model="agency_email" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-medium text-slate-700 mb-0.5">Office Address</label>
                        <textarea wire:model="agency_address" rows="2" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded"></textarea>
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Invoice Prefix</label>
                        <input type="text" wire:model="invoice_prefix" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded font-mono">
                    </div>
                    <div>
                        <label class="block font-medium text-slate-700 mb-0.5">Default Shift Hours / OT Multiplier</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" wire:model="default_shift_hours" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded" placeholder="8">
                            <input type="number" step="0.1" wire:model="overtime_multiplier" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded" placeholder="1.5">
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-medium text-slate-700 mb-0.5">Invoice Footer Note</label>
                        <input type="text" wire:model="invoice_footer" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded">
                    </div>
                </div>
                <button type="submit" class="px-4 py-1.5 rounded bg-slate-900 hover:bg-slate-800 text-white font-semibold">Save Business Settings</button>
            </form>
        </div>
    @endif
</div>
