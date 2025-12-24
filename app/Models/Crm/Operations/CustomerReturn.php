<?php

namespace App\Models\Crm\Operations;

use App\Enums\Crm\CustomerReturnStatus;
use App\Models\Crm\Parties\Customer;
use App\Models\Concerns\EnforcesStatusTransitions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerReturn extends Model
{
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'customer_id',
        'description',
        'status',
        'received_at',
        'reported_by',
        'notes',
    ];

    protected $casts = [
        'status' => CustomerReturnStatus::class,
        'received_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CustomerReturnItem::class);
    }

    public function isClosedOrCancelled(): bool
    {
        $status = $this->status instanceof CustomerReturnStatus
            ? $this->status
            : CustomerReturnStatus::from($this->status);

        return in_array($status, [CustomerReturnStatus::Closed, CustomerReturnStatus::Cancelled], true);
    }

    protected static function statusEnumClass(): string
    {
        return CustomerReturnStatus::class;
    }
}
