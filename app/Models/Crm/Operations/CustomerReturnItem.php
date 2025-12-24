<?php

namespace App\Models\Crm\Operations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerReturnItem extends Model
{
    protected $fillable = [
        'customer_return_id',
        'item_name',
        'quantity',
        'notes',
    ];

    public function customerReturn(): BelongsTo
    {
        return $this->belongsTo(CustomerReturn::class);
    }
}
