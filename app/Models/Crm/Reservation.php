<?php

namespace App\Models\Crm;

use App\Enums\ReservationStatus;
use App\Models\Concerns\EnforcesStatusTransitions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'vehicle_id',
        'status',
        'reserved_from',
        'reserved_until',
        'notes',
    ];

    protected $casts = [
        'status' => ReservationStatus::class,
        'reserved_from' => 'datetime',
        'reserved_until' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    protected static function statusEnumClass(): string
    {
        return ReservationStatus::class;
    }
}
