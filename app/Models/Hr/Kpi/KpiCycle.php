<?php

namespace App\Models\Hr\Kpi;

use App\Enums\Hr\KpiCycleStatus;
use App\Enums\Hr\PeriodType;
use App\Models\Concerns\EnforcesStatusTransitions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiCycle extends Model
{
    use EnforcesStatusTransitions;

    protected $fillable = [
        'period_type',
        'period_start',
        'period_end',
        'label',
        'status',
    ];

    protected $casts = [
        'period_type' => PeriodType::class,
        'period_start' => 'date',
        'period_end' => 'date',
        'status' => KpiCycleStatus::class,
    ];

    public function reports(): HasMany
    {
        return $this->hasMany(KpiReport::class);
    }

    protected static function statusEnumClass(): string
    {
        return KpiCycleStatus::class;
    }
}
