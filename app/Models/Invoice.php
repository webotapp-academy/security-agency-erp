<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'client_id',
        'site_id',
        'billing_period',
        'issue_date',
        'due_date',
        'guard_count',
        'rate_per_guard',
        'subtotal',
        'additional_charges',
        'additional_charges_desc',
        'discount_amount',
        'gst_rate',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'rate_per_guard' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'additional_charges' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'gst_rate' => 'decimal:2',
            'cgst_amount' => 'decimal:2',
            'sgst_amount' => 'decimal:2',
            'igst_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
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

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class)->orderBy('payment_date', 'desc');
    }
}
