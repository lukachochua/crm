<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Models\Concerns\AssignsCreator;
use App\Models\Concerns\EnforcesStatusTransitions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use AssignsCreator;
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'amount',
        'status',
        'payment_date',
        'created_by',
        'payment_method',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function statusEnumClass(): string
    {
        return PaymentStatus::class;
    }
}
