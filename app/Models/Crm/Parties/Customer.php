<?php

namespace App\Models\Crm\Parties;

use App\Models\Crm\Sales\Application;
use App\Models\Crm\Sales\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'personal_id_or_tax_id',
        'phone',
        'email',
        'address',
        'notes',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(CustomerContract::class);
    }

    public function pricingProfiles(): HasMany
    {
        return $this->hasMany(CustomerPricingProfile::class);
    }
}
