<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'avatar',
        'is_active',
        'permissions',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'permissions' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isHr(): bool
    {
        return $this->role === 'hr_manager' || $this->isAdmin();
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'site_supervisor';
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant' || $this->isAdmin();
    }

    public function isOperations(): bool
    {
        return $this->role === 'operations_manager' || $this->isAdmin();
    }

    public function canAccessModule(string $module): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        // Module permission matrix
        $rolePermissions = [
            'hr_manager' => ['dashboard', 'guards', 'duty_roster', 'attendance', 'leaves', 'payroll', 'reports'],
            'operations_manager' => ['dashboard', 'guards', 'clients', 'sites', 'deployments', 'duty_roster', 'attendance', 'equipment', 'incidents', 'reports'],
            'accountant' => ['dashboard', 'clients', 'payroll', 'billing', 'expenses', 'reports'],
            'site_supervisor' => ['dashboard', 'sites', 'deployments', 'duty_roster', 'attendance', 'incidents'],
        ];

        $allowed = $rolePermissions[$this->role] ?? [];
        if (in_array($module, $allowed)) {
            return true;
        }

        if (is_array($this->permissions) && in_array($module, $this->permissions)) {
            return true;
        }

        return false;
    }

    public function supervisedSites(): HasMany
    {
        return $this->hasMany(Site::class, 'supervisor_id');
    }
}
