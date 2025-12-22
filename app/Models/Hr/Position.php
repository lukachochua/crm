<?php

namespace App\Models\Hr;

use App\Models\Hr\Kpi\KpiTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'notes',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function kpiTemplates(): HasMany
    {
        return $this->hasMany(KpiTemplate::class);
    }
}
