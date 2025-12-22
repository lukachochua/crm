<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Customer;
use App\Models\Hr\Employee;
use App\Models\Hr\EmployeeDocument;
use App\Models\Hr\Feedback\FeedbackCycle;
use App\Models\Hr\Feedback\FeedbackRequest;
use App\Models\Hr\Branch;
use App\Models\Hr\ContractType;
use App\Models\Hr\Department;
use App\Models\Hr\Kpi\KpiCycle;
use App\Models\Hr\Kpi\KpiReport;
use App\Models\Hr\Kpi\KpiReportItem;
use App\Models\Hr\Kpi\KpiTemplate;
use App\Models\Hr\Onboarding\EmployeeOnboarding;
use App\Models\Hr\Onboarding\EmployeeOnboardingTask;
use App\Models\Hr\Onboarding\OnboardingTemplate;
use App\Models\Hr\Position;
use App\Models\Hr\Recruitment\Candidate;
use App\Models\Hr\Survey\EngagementSurvey;
use App\Models\Hr\Survey\SurveySubmission;
use App\Models\Hr\Training\TrainingSession;
use App\Models\Hr\Training\TrainingParticipant;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Observers\ApplicationObserver;
use App\Observers\CustomerObserver;
use App\Observers\Hr\BranchObserver;
use App\Observers\Hr\CandidateObserver;
use App\Observers\Hr\ContractTypeObserver;
use App\Observers\Hr\DepartmentObserver;
use App\Observers\Hr\EmployeeObserver;
use App\Observers\Hr\EmployeeDocumentObserver;
use App\Observers\Hr\EmployeeOnboardingObserver;
use App\Observers\Hr\EmployeeOnboardingTaskObserver;
use App\Observers\Hr\EngagementSurveyObserver;
use App\Observers\Hr\FeedbackCycleObserver;
use App\Observers\Hr\FeedbackRequestObserver;
use App\Observers\Hr\KpiCycleObserver;
use App\Observers\Hr\KpiReportObserver;
use App\Observers\Hr\KpiReportItemObserver;
use App\Observers\Hr\KpiTemplateObserver;
use App\Observers\Hr\OnboardingTemplateObserver;
use App\Observers\Hr\PositionObserver;
use App\Observers\Hr\SurveySubmissionObserver;
use App\Observers\Hr\TrainingParticipantObserver;
use App\Observers\Hr\TrainingSessionObserver;
use App\Observers\InvoiceObserver;
use App\Observers\OrderObserver;
use App\Observers\PaymentObserver;
use App\Observers\ReservationObserver;
use App\Observers\VehicleObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('local') && ! $this->app->runningInConsole()) {
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        }

        Application::observe(ApplicationObserver::class);
        Order::observe(OrderObserver::class);
        Reservation::observe(ReservationObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Payment::observe(PaymentObserver::class);
        Customer::observe(CustomerObserver::class);
        Vehicle::observe(VehicleObserver::class);
        Department::observe(DepartmentObserver::class);
        Position::observe(PositionObserver::class);
        Branch::observe(BranchObserver::class);
        ContractType::observe(ContractTypeObserver::class);
        Employee::observe(EmployeeObserver::class);
        EmployeeDocument::observe(EmployeeDocumentObserver::class);
        Candidate::observe(CandidateObserver::class);
        KpiTemplate::observe(KpiTemplateObserver::class);
        KpiCycle::observe(KpiCycleObserver::class);
        KpiReport::observe(KpiReportObserver::class);
        KpiReportItem::observe(KpiReportItemObserver::class);
        TrainingSession::observe(TrainingSessionObserver::class);
        TrainingParticipant::observe(TrainingParticipantObserver::class);
        OnboardingTemplate::observe(OnboardingTemplateObserver::class);
        EmployeeOnboarding::observe(EmployeeOnboardingObserver::class);
        EmployeeOnboardingTask::observe(EmployeeOnboardingTaskObserver::class);
        FeedbackCycle::observe(FeedbackCycleObserver::class);
        FeedbackRequest::observe(FeedbackRequestObserver::class);
        EngagementSurvey::observe(EngagementSurveyObserver::class);
        SurveySubmission::observe(SurveySubmissionObserver::class);
    }
}
