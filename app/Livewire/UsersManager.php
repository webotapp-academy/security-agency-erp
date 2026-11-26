<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;

class UsersManager extends Component
{
    public $showModal = false;
    public $isEditing = false;
    public $userId = null;

    public $name = '';
    public $email = '';
    public $phone = '';
    public $role = 'site_supervisor';
    public $password = '';
    public $is_active = true;

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->isEditing = false;
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->role = 'site_supervisor';
        $this->password = '';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $this->isEditing = true;
        $this->userId = $id;
        $u = User::findOrFail($id);
        $this->name = $u->name;
        $this->email = $u->email;
        $this->phone = $u->phone;
        $this->role = $u->role;
        $this->password = '';
        $this->is_active = $u->is_active;
        $this->showModal = true;
    }

    public function saveUser()
    {
        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required|in:admin,hr_manager,site_supervisor,accountant,operations_manager',
        ];
        if (!$this->isEditing) {
            $rules['password'] = 'required|min:6';
        }
        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEditing) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            AuditLog::log('user_updated', 'users', $user->id, "Updated staff user {$user->name}");
            session()->flash('success', "Staff user {$user->name} updated.");
        } else {
            $user = User::create($data);
            AuditLog::log('user_created', 'users', $user->id, "Created staff user {$user->name} ({$user->role})");
            session()->flash('success', "Staff user {$user->name} created.");
        }

        $this->showModal = false;
    }

    public function render()
    {
        $users = User::withCount('supervisedSites')->orderBy('id')->get();
        return view('livewire.users-manager', compact('users'))
            ->layout('layouts.app', ['title' => 'User & Staff Management', 'subtitle' => 'Role-Based Access Control']);
    }
}
