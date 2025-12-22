<?php

namespace App\Models\Hr\Kpi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiReportItem extends Model
{
    protected $fillable = [
        'kpi_report_id',
        'kpi_template_item_id',
        'self_score',
        'manager_score',
        'computed_score',
        'self_comment',
        'manager_comment',
    ];

    protected $casts = [
        'self_score' => 'decimal:2',
        'manager_score' => 'decimal:2',
        'computed_score' => 'decimal:2',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(KpiReport::class, 'kpi_report_id');
    }

    public function templateItem(): BelongsTo
    {
        return $this->belongsTo(KpiTemplateItem::class, 'kpi_template_item_id');
    }
}
