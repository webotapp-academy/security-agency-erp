<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deployment extends Model
{
    protected $fillable = [
        'guard_id',
        'site_id',
        'shift_id',
        'designation',
        'start_date',
        'end_date',
        'duty_instructions',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
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

    public function relieve(?string $endDate = null, ?string $reason = null): void
    {
        $this->update([
            'status' => 'relieved',
            'end_date' => $endDate ?? now()->toDateString(),
            'notes' => $this->notes . ($reason ? " [Relieved: {$reason}]" : " [Relieved on " . now()->toDateString() . "]"),
        ]);
    }
}
