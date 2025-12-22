<?php

namespace App\Observers\Hr;

use App\Models\Hr\Kpi\KpiReportItem;
use App\Services\Hr\KpiScoreCalculator;

class KpiReportItemObserver
{
    public function saved(KpiReportItem $item): void
    {
        $this->recalculate($item);
    }

    public function deleted(KpiReportItem $item): void
    {
        $this->recalculate($item);
    }

    private function recalculate(KpiReportItem $item): void
    {
        if (! $item->report) {
            $item->loadMissing('report');
        }

        if (! $item->report) {
            return;
        }

        (new KpiScoreCalculator())->recalculate($item->report);
    }
}
