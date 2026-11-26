<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DutyRoster extends Model
{
    protected $fillable = [
        'site_id',
        'guard_id',
        'shift_id',
        'roster_date',
        'status',
        'replacement_guard_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'roster_date' => 'date',
        ];
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function securityGuard(): BelongsTo
    {
        return $this->belongsTo(Guard::class, 'guard_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function replacementGuard(): BelongsTo
    {
        return $this->belongsTo(Guard::class, 'replacement_guard_id');
    }
}
