<?php

namespace App\Filament\Widgets\Hr;

use App\Enums\Hr\EmployeeStatus;
use App\Enums\Hr\FeedbackRequestStatus;
use App\Enums\Hr\OnboardingStatus;
use App\Enums\Hr\SurveyStatus;
use App\Filament\Resources\Hr\EmployeeOnboardingResource;
use App\Filament\Resources\Hr\EmployeeResource;
use App\Filament\Resources\Hr\EngagementSurveyResource;
use App\Filament\Resources\Hr\FeedbackRequestResource;
use App\Models\Hr\Employee;
use App\Models\Hr\Feedback\FeedbackRequest;
use App\Models\Hr\Onboarding\EmployeeOnboarding;
use App\Models\Hr\Survey\EngagementSurvey;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HrStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -30;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'HR Overview';

    protected ?string $description = 'Key people, onboarding, and feedback signals.';

    public static function canView(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('superadmin')
            || $user->hasAnyRole(['hr_admin', 'hr_manager', 'department_manager']);
    }

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $contractWindowEnd = $today->copy()->addDays(30)->endOfDay();

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', EmployeeStatus::Active->value)->count();
        $expiringContracts = Employee::query()
            ->whereNotNull('contract_end_date')
            ->whereBetween('contract_end_date', [$today, $contractWindowEnd])
            ->count();

        $activeOnboardings = EmployeeOnboarding::query()
            ->whereIn('status', [
                OnboardingStatus::NotStarted->value,
                OnboardingStatus::InProgress->value,
            ])
            ->count();

        $pendingFeedback = FeedbackRequest::where('status', FeedbackRequestStatus::Pending->value)->count();
        $openSurveys = EngagementSurvey::where('status', SurveyStatus::Open->value)->count();

        return [
            Stat::make('Employees', number_format($totalEmployees))
                ->description(number_format($activeEmployees) . ' active')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->url(EmployeeResource::getUrl()),
            Stat::make('Contracts Expiring', number_format($expiringContracts))
                ->description('Next 30 days')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url(EmployeeResource::getUrl()),
            Stat::make('Active Onboarding', number_format($activeOnboardings))
                ->description('Not started or in progress')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info')
                ->url(EmployeeOnboardingResource::getUrl()),
            Stat::make('Pending Feedback', number_format($pendingFeedback))
                ->description('Requests awaiting submission')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('danger')
                ->url(FeedbackRequestResource::getUrl()),
            Stat::make('Open Surveys', number_format($openSurveys))
                ->description('Engagement surveys open')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success')
                ->url(EngagementSurveyResource::getUrl()),
        ];
    }
}
