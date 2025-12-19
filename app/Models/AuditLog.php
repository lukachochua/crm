<?php

namespace App\Models;

use App\Enums\AuditActionType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'action_type',
        'performed_by',
        'performed_at',
        'before_state',
        'after_state',
        'amount_before',
        'amount_after',
        'currency',
        'notes',
        'ip_address',
    ];

    protected $casts = [
        'action_type' => AuditActionType::class,
        'performed_at' => 'datetime',
        'before_state' => 'array',
        'after_state' => 'array',
        'amount_before' => 'decimal:2',
        'amount_after' => 'decimal:2',
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
