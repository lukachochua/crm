<?php

namespace App\Models\Crm\Operations;

use App\Enums\Crm\InternalTransferStatus;
use App\Models\Concerns\EnforcesStatusTransitions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalTransfer extends Model
{
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'source_location',
        'destination_location',
        'description',
        'status',
        'requested_by',
        'requested_at',
        'notes',
    ];

    protected $casts = [
        'status' => InternalTransferStatus::class,
        'requested_at' => 'datetime',
    ];

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function isClosedOrCancelled(): bool
    {
        $status = $this->status instanceof InternalTransferStatus
            ? $this->status
            : InternalTransferStatus::from($this->status);

        return in_array($status, [InternalTransferStatus::Closed, InternalTransferStatus::Cancelled], true);
    }

    protected static function statusEnumClass(): string
    {
        return InternalTransferStatus::class;
    }
}
