<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'payroll_code',
        'guard_id',
        'month',
        'year',
        'salary_type',
        'basic_salary',
        'allowances',
        'overtime_hours',
        'overtime_rate',
        'overtime_amount',
        'total_working_days',
        'present_days',
        'leave_deductions',
        'advance_deductions',
        'other_deductions',
        'gross_salary',
        'net_salary',
        'status',
        'payment_date',
        'payment_mode',
        'payment_reference',
        'notes',
        'generated_by_id',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'basic_salary' => 'decimal:2',
            'allowances' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'overtime_rate' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'present_days' => 'decimal:1',
            'leave_deductions' => 'decimal:2',
            'advance_deductions' => 'decimal:2',
            'other_deductions' => 'decimal:2',
            'gross_salary' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    public function securityGuard(): BelongsTo
    {
        return $this->belongsTo(Guard::class, 'guard_id');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by_id');
    }

    public function getMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 10));
    }

    public function getPeriodAttribute(): string
    {
        return "{$this->month_name} {$this->year}";
    }
}
