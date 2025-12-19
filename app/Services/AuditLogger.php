<?php

namespace App\Services;

use App\Enums\AuditActionType;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use RuntimeException;

class AuditLogger
{
    public static function record(
        Model $model,
        AuditActionType $actionType,
        array $beforeState = [],
        array $afterState = [],
        ?string $amountBefore = null,
        ?string $amountAfter = null,
        ?string $currency = null,
        ?string $notes = null
    ): void {
        $performedBy = Auth::id();

        if (! $performedBy) {
            throw new RuntimeException('performed_by is required for audit logging.');
        }

        AuditLog::create([
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'action_type' => $actionType->value,
            'performed_by' => $performedBy,
            'performed_at' => now(),
            'before_state' => $beforeState,
            'after_state' => $afterState,
            'amount_before' => $amountBefore,
            'amount_after' => $amountAfter,
            'currency' => $currency,
            'notes' => $notes,
            'ip_address' => Request::ip(),
        ]);
    }
}
