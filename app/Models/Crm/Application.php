<?php

namespace App\Models\Crm;

use App\Enums\ApplicationStatus;
use App\Models\Concerns\AssignsCreator;
use App\Models\Concerns\EnforcesStatusTransitions;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use AssignsCreator;
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'status',
        'requested_at',
        'created_by',
        'description',
        'source',
        'internal_notes',
    ];

    protected $casts = [
        'status' => ApplicationStatus::class,
        'requested_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function statusEnumClass(): string
    {
        return ApplicationStatus::class;
    }
}
