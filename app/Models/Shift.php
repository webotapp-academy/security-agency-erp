<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'code',
        'start_time',
        'end_time',
        'duration_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'duration_hours' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    public function dutyRosters(): HasMany
    {
        return $this->hasMany(DutyRoster::class);
    }

    public function getFormattedTimeAttribute(): string
    {
        $start = substr($this->start_time, 0, 5);
        $end = substr($this->end_time, 0, 5);
        return "{$start} - {$end}";
    }
}
