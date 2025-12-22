<?php

namespace App\Models\Hr\Onboarding;

use App\Models\Hr\Department;
use App\Models\Hr\Position;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnboardingTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'department_id',
        'position_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(OnboardingTemplateTask::class);
    }

    public function employeeOnboardings(): HasMany
    {
        return $this->hasMany(EmployeeOnboarding::class);
    }
}
