<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Models\Concerns\EnforcesStatusTransitions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'status',
        'total_amount',
        'issued_at',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'status' => InvoiceStatus::class,
        'total_amount' => 'decimal:2',
        'issued_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    protected static function statusEnumClass(): string
    {
        return InvoiceStatus::class;
    }
}
