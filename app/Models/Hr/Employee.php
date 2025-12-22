<?php

namespace App\Models\Hr;

use App\Enums\Hr\EmployeeStatus;
use App\Models\Concerns\EnforcesStatusTransitions;
use App\Models\Hr\Feedback\FeedbackRequest;
use App\Models\Hr\Kpi\KpiReport;
use App\Models\Hr\Onboarding\EmployeeOnboarding;
use App\Models\Hr\Training\TrainingParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use EnforcesStatusTransitions;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'position_id',
        'branch_id',
        'contract_type_id',
        'manager_user_id',
        'start_date',
        'contract_end_date',
        'status',
        'notes',
        'feedback_summary',
        'feedback_last_calculated_at',
    ];

    protected $casts = [
        'status' => EmployeeStatus::class,
        'start_date' => 'date',
        'contract_end_date' => 'date',
        'feedback_summary' => 'array',
        'feedback_last_calculated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function kpiReports(): HasMany
    {
        return $this->hasMany(KpiReport::class);
    }

    public function trainingParticipants(): HasMany
    {
        return $this->hasMany(TrainingParticipant::class);
    }

    public function onboardings(): HasMany
    {
        return $this->hasMany(EmployeeOnboarding::class);
    }

    public function feedbackRequests(): HasMany
    {
        return $this->hasMany(FeedbackRequest::class);
    }

    protected static function statusEnumClass(): string
    {
        return EmployeeStatus::class;
    }
}
