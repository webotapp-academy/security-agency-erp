<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuardDocument extends Model
{
    protected $fillable = [
        'guard_id',
        'document_type',
        'document_title',
        'document_number',
        'file_path',
        'expiry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
        ];
    }

    public function securityGuard(): BelongsTo
    {
        return $this->belongsTo(Guard::class, 'guard_id');
    }
}
