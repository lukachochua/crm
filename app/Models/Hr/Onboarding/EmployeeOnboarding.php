<?php

namespace App\Models\Hr\Onboarding;

use App\Enums\Hr\OnboardingStatus;
use App\Models\Concerns\EnforcesStatusTransitions;
use App\Models\Hr\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeOnboarding extends Model
{
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'onboarding_template_id',
        'status',
        'start_date',
        'due_date',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'status' => OnboardingStatus::class,
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'onboarding_template_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(EmployeeOnboardingTask::class);
    }

    protected static function statusEnumClass(): string
    {
        return OnboardingStatus::class;
    }
}
