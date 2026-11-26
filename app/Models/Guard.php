<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guard extends Model
{
    use HasFactory;

    protected $fillable = [
        'guard_code',
        'full_name',
        'father_name',
        'photo',
        'dob',
        'gender',
        'phone',
        'alternate_phone',
        'email',
        'permanent_address',
        'current_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'aadhaar_reference',
        'pan_number',
        'police_verification_status',
        'police_verification_ref',
        'background_verification_status',
        'joining_date',
        'employment_type',
        'designation',
        'qualification',
        'training_details',
        'experience_years',
        'blood_group',
        'bank_name',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'salary_type',
        'basic_salary',
        'allowances',
        'overtime_rate',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'joining_date' => 'date',
            'basic_salary' => 'decimal:2',
            'allowances' => 'decimal:2',
            'overtime_rate' => 'decimal:2',
        ];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(GuardDocument::class);
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class)->orderBy('start_date', 'desc');
    }

    public function activeDeployment(): HasOne
    {
        return $this->hasOne(Deployment::class)->where('status', 'active');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function salaryAdvances(): HasMany
    {
        return $this->hasMany(SalaryAdvance::class);
    }

    public function equipmentAssignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getIsDeployedAttribute(): bool
    {
        return $this->deployments()->where('status', 'active')->exists();
    }

    public function getMaskedAadhaarAttribute(): ?string
    {
        if (!$this->aadhaar_reference) return null;
        $len = strlen($this->aadhaar_reference);
        if ($len <= 4) return 'XXXX-XXXX-' . $this->aadhaar_reference;
        return 'XXXX-XXXX-' . substr($this->aadhaar_reference, -4);
    }
}
