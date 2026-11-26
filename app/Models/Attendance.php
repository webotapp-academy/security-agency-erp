<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    protected $fillable = [
        'guard_id',
        'site_id',
        'shift_id',
        'attendance_date',
        'status',
        'check_in_time',
        'check_out_time',
        'late_minutes',
        'overtime_hours',
        'verified_by_id',
        'remarks',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'overtime_hours' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function securityGuard(): BelongsTo
    {
        return $this->belongsTo(Guard::class, 'guard_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(AttendanceAudit::class);
    }
}
