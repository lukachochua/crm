<?php

namespace App\Services\Hr;

use App\Enums\Hr\PeriodType;
use App\Models\Hr\Kpi\KpiCycle;
use App\Models\Hr\Kpi\KpiReport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class HrReportService
{
    public function kpiDepartmentSummary(KpiCycle $cycle): Collection
    {
        return KpiReport::query()
            ->select('employees.department_id', DB::raw('avg(computed_score) as average_score'), DB::raw('count(*) as report_count'))
            ->join('employees', 'employees.id', '=', 'kpi_reports.employee_id')
            ->where('kpi_reports.kpi_cycle_id', $cycle->id)
            ->groupBy('employees.department_id')
            ->get();
    }

    public function kpiPeriodSummary(PeriodType $periodType, int $year): Collection
    {
        return KpiCycle::query()
            ->select('kpi_cycles.id', 'kpi_cycles.label', DB::raw('avg(kpi_reports.computed_score) as average_score'))
            ->leftJoin('kpi_reports', 'kpi_reports.kpi_cycle_id', '=', 'kpi_cycles.id')
            ->where('kpi_cycles.period_type', $periodType->value)
            ->whereYear('kpi_cycles.period_start', $year)
            ->groupBy('kpi_cycles.id', 'kpi_cycles.label')
            ->get();
    }
}
