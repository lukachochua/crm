<?php

namespace App\Models\Hr\Onboarding;

use App\Enums\Hr\OnboardingTaskStatus;
use App\Models\Concerns\EnforcesStatusTransitions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeOnboardingTask extends Model
{
    use EnforcesStatusTransitions;

    protected $fillable = [
        'employee_onboarding_id',
        'onboarding_template_task_id',
        'assigned_to_user_id',
        'status',
        'due_date',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'status' => OnboardingTaskStatus::class,
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function onboarding(): BelongsTo
    {
        return $this->belongsTo(EmployeeOnboarding::class, 'employee_onboarding_id');
    }

    public function templateTask(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplateTask::class, 'onboarding_template_task_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    protected static function statusEnumClass(): string
    {
        return OnboardingTaskStatus::class;
    }
}
