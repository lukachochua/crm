<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Customer;
use App\Models\Hr\Branch;
use App\Models\Hr\ContractType;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use App\Models\Hr\EmployeeDocument;
use App\Models\Hr\Feedback\FeedbackCycle;
use App\Models\Hr\Feedback\FeedbackAnswer;
use App\Models\Hr\Feedback\FeedbackQuestion;
use App\Models\Hr\Feedback\FeedbackRequest;
use App\Models\Hr\Kpi\KpiCycle;
use App\Models\Hr\Kpi\KpiReport;
use App\Models\Hr\Kpi\KpiReportItem;
use App\Models\Hr\Kpi\KpiTemplate;
use App\Models\Hr\Kpi\KpiTemplateItem;
use App\Models\Hr\Onboarding\EmployeeOnboarding;
use App\Models\Hr\Onboarding\EmployeeOnboardingTask;
use App\Models\Hr\Onboarding\OnboardingTemplate;
use App\Models\Hr\Onboarding\OnboardingTemplateTask;
use App\Models\Hr\Position;
use App\Models\Hr\Recruitment\Candidate;
use App\Models\Hr\Survey\EngagementSurvey;
use App\Models\Hr\Survey\SurveyAnswer;
use App\Models\Hr\Survey\SurveyQuestion;
use App\Models\Hr\Survey\SurveySubmission;
use App\Models\Hr\Training\TrainingParticipant;
use App\Models\Hr\Training\TrainingSession;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\TurnoverOverview;
use App\Models\Vehicle;
use App\Policies\ApplicationPolicy;
use App\Policies\Hr\BranchPolicy;
use App\Policies\Hr\CandidatePolicy;
use App\Policies\Hr\ContractTypePolicy;
use App\Policies\Hr\DepartmentPolicy;
use App\Policies\Hr\EmployeeDocumentPolicy;
use App\Policies\Hr\EmployeeOnboardingPolicy;
use App\Policies\Hr\EmployeePolicy;
use App\Policies\Hr\EngagementSurveyPolicy;
use App\Policies\Hr\FeedbackCyclePolicy;
use App\Policies\Hr\FeedbackAnswerPolicy;
use App\Policies\Hr\FeedbackQuestionPolicy;
use App\Policies\Hr\FeedbackRequestPolicy;
use App\Policies\Hr\KpiCyclePolicy;
use App\Policies\Hr\KpiReportPolicy;
use App\Policies\Hr\KpiReportItemPolicy;
use App\Policies\Hr\KpiTemplatePolicy;
use App\Policies\Hr\KpiTemplateItemPolicy;
use App\Policies\Hr\OnboardingTemplatePolicy;
use App\Policies\Hr\OnboardingTemplateTaskPolicy;
use App\Policies\Hr\PositionPolicy;
use App\Policies\Hr\EmployeeOnboardingTaskPolicy;
use App\Policies\Hr\SurveyAnswerPolicy;
use App\Policies\Hr\SurveyQuestionPolicy;
use App\Policies\Hr\SurveySubmissionPolicy;
use App\Policies\Hr\TrainingParticipantPolicy;
use App\Policies\Hr\TrainingSessionPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ReservationPolicy;
use App\Policies\TurnoverOverviewPolicy;
use App\Policies\VehiclePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Application::class => ApplicationPolicy::class,
        Order::class => OrderPolicy::class,
        Reservation::class => ReservationPolicy::class,
        Customer::class => CustomerPolicy::class,
        Vehicle::class => VehiclePolicy::class,
        Invoice::class => InvoicePolicy::class,
        Payment::class => PaymentPolicy::class,
        TurnoverOverview::class => TurnoverOverviewPolicy::class,
        Department::class => DepartmentPolicy::class,
        Position::class => PositionPolicy::class,
        Branch::class => BranchPolicy::class,
        ContractType::class => ContractTypePolicy::class,
        Employee::class => EmployeePolicy::class,
        EmployeeDocument::class => EmployeeDocumentPolicy::class,
        KpiTemplate::class => KpiTemplatePolicy::class,
        KpiTemplateItem::class => KpiTemplateItemPolicy::class,
        KpiCycle::class => KpiCyclePolicy::class,
        KpiReport::class => KpiReportPolicy::class,
        KpiReportItem::class => KpiReportItemPolicy::class,
        TrainingSession::class => TrainingSessionPolicy::class,
        TrainingParticipant::class => TrainingParticipantPolicy::class,
        Candidate::class => CandidatePolicy::class,
        OnboardingTemplate::class => OnboardingTemplatePolicy::class,
        OnboardingTemplateTask::class => OnboardingTemplateTaskPolicy::class,
        EmployeeOnboarding::class => EmployeeOnboardingPolicy::class,
        EmployeeOnboardingTask::class => EmployeeOnboardingTaskPolicy::class,
        FeedbackCycle::class => FeedbackCyclePolicy::class,
        FeedbackQuestion::class => FeedbackQuestionPolicy::class,
        FeedbackRequest::class => FeedbackRequestPolicy::class,
        FeedbackAnswer::class => FeedbackAnswerPolicy::class,
        EngagementSurvey::class => EngagementSurveyPolicy::class,
        SurveyQuestion::class => SurveyQuestionPolicy::class,
        SurveySubmission::class => SurveySubmissionPolicy::class,
        SurveyAnswer::class => SurveyAnswerPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
