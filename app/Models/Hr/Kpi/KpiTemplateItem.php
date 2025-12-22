<?php

namespace App\Models\Hr\Kpi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiTemplateItem extends Model
{
    protected $fillable = [
        'kpi_template_id',
        'title',
        'description',
        'weight',
        'sort_order',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(KpiTemplate::class, 'kpi_template_id');
    }

    public function reportItems(): HasMany
    {
        return $this->hasMany(KpiReportItem::class);
    }
}
