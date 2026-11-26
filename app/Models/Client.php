<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_code',
        'client_name',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'billing_address',
        'gstin',
        'state',
        'pincode',
        'contract_start_date',
        'contract_end_date',
        'contract_amount',
        'payment_terms',
        'contract_document',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
            'contract_amount' => 'decimal:2',
        ];
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->orderBy('issue_date', 'desc');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return (float) $this->invoices()->whereIn('status', ['sent', 'partially_paid', 'overdue'])->sum('balance_amount');
    }

    public function getActiveSitesCountAttribute(): int
    {
        return $this->sites()->where('status', 'active')->count();
    }

    public function getIsContractExpiringSoonAttribute(): bool
    {
        if (!$this->contract_end_date) return false;
        return $this->contract_end_date->isFuture() && $this->contract_end_date->diffInDays(now()) <= 30;
    }
}
