<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurnoverOverview extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = 'period';

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'total_invoiced' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
    ];
}
