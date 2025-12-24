<?php

namespace App\Models\Crm\Parties;

use App\Enums\Crm\ContractStatus;
use App\Models\Concerns\AssignsCreator;
use App\Models\Concerns\EnforcesStatusTransitions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerContract extends Model
{
    use AssignsCreator;
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $table = 'crm_customer_contracts';

    protected $fillable = [
        'customer_id',
        'contract_number',
        'contract_type',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => ContractStatus::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function statusEnumClass(): string
    {
        return ContractStatus::class;
    }
}
