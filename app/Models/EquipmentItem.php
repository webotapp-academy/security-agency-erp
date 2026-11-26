<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentItem extends Model
{
    protected $fillable = [
        'item_code',
        'name',
        'category',
        'total_quantity',
        'available_quantity',
        'unit_cost',
        'condition_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_quantity' => 'integer',
            'available_quantity' => 'integer',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class);
    }
}
