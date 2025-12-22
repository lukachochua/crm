<?php

namespace App\Models\Hr\Kpi;

use App\Enums\Hr\KpiReportStatus;
use App\Models\Concerns\EnforcesStatusTransitions;
use App\Models\Hr\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiReport extends Model
{
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'kpi_template_id',
        'kpi_cycle_id',
        'status',
        'self_submitted_at',
        'manager_reviewed_at',
        'self_score_total',
        'manager_score_total',
        'computed_score',
    ];

    protected $casts = [
        'status' => KpiReportStatus::class,
        'self_submitted_at' => 'datetime',
        'manager_reviewed_at' => 'datetime',
        'self_score_total' => 'decimal:2',
        'manager_score_total' => 'decimal:2',
        'computed_score' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(KpiTemplate::class, 'kpi_template_id');
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(KpiCycle::class, 'kpi_cycle_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(KpiReportItem::class);
    }

    protected static function statusEnumClass(): string
    {
        return KpiReportStatus::class;
    }
}
