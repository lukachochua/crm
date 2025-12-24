<?php

namespace App\Models\Crm\Parties;

use App\Models\Concerns\AssignsCreator;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPricingProfile extends Model
{
    use AssignsCreator;
    use SoftDeletes;

    protected $table = 'crm_customer_pricing_profiles';

    protected $fillable = [
        'customer_id',
        'pricing_type',
        'discount_percent',
        'currency_code',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
