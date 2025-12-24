<?php

namespace App\Providers;

use App\Models\Crm\Sales\Application;
use App\Models\Crm\Parties\Customer;
use App\Models\Crm\Parties\CustomerContract;
use App\Models\Crm\Parties\CustomerPricingProfile;
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
use App\Models\Crm\Billing\Invoice;
use App\Models\Crm\Sales\Order;
use App\Models\Crm\Billing\Payment;
use App\Models\Crm\Sales\Reservation;
use App\Models\Crm\Reporting\TurnoverOverview;
use App\Models\Crm\Assets\Vehicle;
use App\Models\Crm\Operations\CustomerReturn;
use App\Models\Crm\Operations\InternalTransfer;
use App\Observers\Crm\ApplicationObserver;
use App\Observers\Crm\CustomerObserver;
use App\Observers\Crm\CustomerContractObserver;
use App\Observers\Crm\CustomerPricingProfileObserver;
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
use App\Observers\Crm\InvoiceObserver;
use App\Observers\Crm\OrderObserver;
use App\Observers\Crm\PaymentObserver;
use App\Observers\Crm\ReservationObserver;
use App\Observers\Crm\VehicleObserver;
use App\Observers\Crm\CustomerReturnObserver;
use App\Observers\Crm\InternalTransferObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
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

        Relation::morphMap([
            'App\\Models\\Application' => Application::class,
            'App\\Models\\Customer' => Customer::class,
            'App\\Models\\Order' => Order::class,
            'App\\Models\\Reservation' => Reservation::class,
            'App\\Models\\Invoice' => Invoice::class,
            'App\\Models\\Payment' => Payment::class,
            'App\\Models\\Vehicle' => Vehicle::class,
            'App\\Models\\TurnoverOverview' => TurnoverOverview::class,
        ]);

        Application::observe(ApplicationObserver::class);
        Order::observe(OrderObserver::class);
        Reservation::observe(ReservationObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Payment::observe(PaymentObserver::class);
        Customer::observe(CustomerObserver::class);
        CustomerContract::observe(CustomerContractObserver::class);
        CustomerPricingProfile::observe(CustomerPricingProfileObserver::class);
        Vehicle::observe(VehicleObserver::class);
        InternalTransfer::observe(InternalTransferObserver::class);
        CustomerReturn::observe(CustomerReturnObserver::class);
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
