<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentAssignment extends Model
{
    protected $fillable = [
        'equipment_item_id',
        'guard_id',
        'quantity',
        'issue_date',
        'return_date',
        'condition_on_issue',
        'condition_on_return',
        'status',
        'penalty_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'return_date' => 'date',
            'quantity' => 'integer',
            'penalty_amount' => 'decimal:2',
        ];
    }

    public function equipmentItem(): BelongsTo
    {
        return $this->belongsTo(EquipmentItem::class);
    }

    public function securityGuard(): BelongsTo
    {
        return $this->belongsTo(Guard::class, 'guard_id');
    }
}
