<?php

namespace App\Models\Hr\Onboarding;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingTemplateTask extends Model
{
    protected $fillable = [
        'onboarding_template_id',
        'title',
        'description',
        'sort_order',
        'default_due_days',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'default_due_days' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(OnboardingTemplate::class, 'onboarding_template_id');
    }
}
