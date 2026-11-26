<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryAdvance extends Model
{
    protected $fillable = [
        'guard_id',
        'advance_code',
        'amount',
        'request_date',
        'status',
        'approved_by_id',
        'recovered_amount',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'amount' => 'decimal:2',
            'recovered_amount' => 'decimal:2',
        ];
    }

    public function securityGuard(): BelongsTo
    {
        return $this->belongsTo(Guard::class, 'guard_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
