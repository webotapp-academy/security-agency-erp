<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    protected $fillable = [
        'incident_code',
        'client_id',
        'site_id',
        'incident_datetime',
        'reported_by',
        'category',
        'description',
        'action_taken',
        'involved_guards',
        'resolution_status',
        'confidential',
        'attachment',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'incident_datetime' => 'datetime',
            'resolved_at' => 'datetime',
            'confidential' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
