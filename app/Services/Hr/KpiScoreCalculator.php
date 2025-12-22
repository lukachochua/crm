<?php

namespace App\Services\Hr;

use App\Models\Hr\Kpi\KpiReport;

class KpiScoreCalculator
{
    public function recalculate(KpiReport $report): void
    {
        $report->loadMissing('items.templateItem');

        $weightTotal = 0.0;
        $selfWeighted = 0.0;
        $managerWeighted = 0.0;
        $computedWeighted = 0.0;

        foreach ($report->items as $item) {
            $weight = (float) ($item->templateItem->weight ?? 0);
            if ($weight <= 0) {
                continue;
            }

            $weightTotal += $weight;

            if ($item->self_score !== null) {
                $selfWeighted += ((float) $item->self_score) * $weight;
            }

            if ($item->manager_score !== null) {
                $managerWeighted += ((float) $item->manager_score) * $weight;
            }

            $computedBase = $item->manager_score ?? $item->self_score;
            if ($computedBase !== null) {
                $computedWeighted += ((float) $computedBase) * $weight;
                $item->computed_score = (float) $computedBase;
                $item->saveQuietly();
            }
        }

        $report->self_score_total = $this->safeDivide($selfWeighted, $weightTotal);
        $report->manager_score_total = $this->safeDivide($managerWeighted, $weightTotal);
        $report->computed_score = $this->safeDivide($computedWeighted, $weightTotal);
        $report->saveQuietly();
    }

    private function safeDivide(float $numerator, float $denominator): ?float
    {
        if ($denominator <= 0) {
            return null;
        }

        return round($numerator / $denominator, 2);
    }
}
