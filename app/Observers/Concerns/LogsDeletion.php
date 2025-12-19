<?php

namespace App\Observers\Concerns;

use App\Enums\AuditActionType;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;

trait LogsDeletion
{
    public function deleted(Model $model): void
    {
        AuditLogger::record(
            $model,
            AuditActionType::Deletion,
            $model->getOriginal(),
            []
        );
    }
}
