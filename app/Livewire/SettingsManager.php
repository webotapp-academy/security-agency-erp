<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Setting;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsManager extends Component
{
    public $activeTab = 'profile'; // 'profile', 'security', 'business'

    // Profile
    public $name = '';
    public $email = '';
    public $phone = '';

    // Security
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    // Business
    public $software_logo_name = '';
    public $agency_name = '';
    public $agency_tagline = '';
    public $agency_phone = '';
    public $agency_email = '';
    public $agency_address = '';
    public $agency_gstin = '';
    public $invoice_prefix = '';
    public $invoice_footer = '';
    public $default_shift_hours = '8';
    public $overtime_multiplier = '1.5';

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;

        $this->software_logo_name = Setting::get('software_logo_name', 'Garuda Apex Security');
        $this->agency_name = Setting::get('agency_name', 'Garuda Apex Security Services Pvt. Ltd.');
        $this->agency_tagline = Setting::get('agency_tagline', '');
        $this->agency_phone = Setting::get('agency_phone', '');
        $this->agency_email = Setting::get('agency_email', '');
        $this->agency_address = Setting::get('agency_address', '');
        $this->agency_gstin = Setting::get('agency_gstin', '');
        $this->invoice_prefix = Setting::get('invoice_prefix', 'GSS-INV');
        $this->invoice_footer = Setting::get('invoice_footer', '');
        $this->default_shift_hours = Setting::get('default_shift_hours', '8');
        $this->overtime_multiplier = Setting::get('overtime_multiplier', '1.5');
    }

    public function saveProfile()
    {
        $user = Auth::user();
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        session()->flash('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $user->update(['password' => Hash::make($this->new_password)]);
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        AuditLog::log('password_changed', 'settings', $user->id, 'User changed account password');
        session()->flash('success', 'Password changed successfully.');
    }

    public function saveBusinessSettings()
    {
        $settings = [
            'software_logo_name' => $this->software_logo_name,
            'agency_name' => $this->agency_name,
            'agency_tagline' => $this->agency_tagline,
            'agency_phone' => $this->agency_phone,
            'agency_email' => $this->agency_email,
            'agency_address' => $this->agency_address,
            'agency_gstin' => $this->agency_gstin,
            'invoice_prefix' => $this->invoice_prefix,
            'invoice_footer' => $this->invoice_footer,
            'default_shift_hours' => $this->default_shift_hours,
            'overtime_multiplier' => $this->overtime_multiplier,
        ];

        foreach ($settings as $k => $v) {
            Setting::set($k, $v, 'business');
        }

        AuditLog::log('settings_updated', 'settings', null, 'Updated agency business settings');
        session()->flash('success', 'Business settings saved.');
    }

    public function render()
    {
        return view('livewire.settings-manager')
            ->layout('layouts.app', ['title' => 'Settings', 'subtitle' => 'Profile, Security & Agency Configuration']);
    }
}
