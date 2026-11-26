<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'site_code',
        'site_name',
        'site_type',
        'address',
        'contact_person',
        'contact_phone',
        'required_guards',
        'supervisor_id',
        'duty_instructions',
        'shift_requirements',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'required_guards' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class)->orderBy('start_date', 'desc');
    }

    public function activeDeployments(): HasMany
    {
        return $this->hasMany(Deployment::class)->where('status', 'active');
    }

    public function dutyRosters(): HasMany
    {
        return $this->hasMany(DutyRoster::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function getActiveGuardsCountAttribute(): int
    {
        return $this->activeDeployments()->count();
    }

    public function getShortageCountAttribute(): int
    {
        $shortage = $this->required_guards - $this->active_guards_count;
        return max(0, $shortage);
    }
}
