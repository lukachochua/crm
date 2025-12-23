<?php

namespace App\Models\Crm\Assets;

use App\Models\Crm\Sales\Reservation;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vin_or_serial',
        'type',
        'status',
        'model',
        'year',
        'color',
        'notes',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
