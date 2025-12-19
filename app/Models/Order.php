<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Concerns\AssignsCreator;
use App\Models\Concerns\EnforcesStatusTransitions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use AssignsCreator;
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'application_id',
        'order_number',
        'status',
        'total_amount',
        'discount_amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function statusEnumClass(): string
    {
        return OrderStatus::class;
    }
}
