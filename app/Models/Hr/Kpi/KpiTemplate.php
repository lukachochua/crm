<?php

namespace App\Models\Hr\Kpi;

use App\Models\Hr\Position;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'position_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(KpiTemplateItem::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(KpiReport::class);
    }
}
