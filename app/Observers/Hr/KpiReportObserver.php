<?php

namespace App\Observers\Hr;

use App\Enums\AuditActionType;
use App\Models\Hr\Kpi\KpiReport;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class KpiReportObserver
{
    use LogsDeletion;

    public function updated(KpiReport $report): void
    {
        if (! $report->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $report,
            AuditActionType::StatusChange,
            $report->getOriginal(),
            $report->getAttributes()
        );
    }
}
