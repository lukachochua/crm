<?php

namespace App\Filament\Widgets\Hr;

use App\Enums\Hr\OnboardingStatus;
use App\Filament\Resources\Hr\EmployeeOnboardingResource;
use App\Models\Hr\Onboarding\EmployeeOnboarding;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class OverdueOnboardingWidget extends TableWidget
{
    protected static ?int $sort = -9;

    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('superadmin')
            || $user->hasAnyRole(['hr_admin', 'hr_manager', 'department_manager']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Overdue Onboarding')
            ->query(
                EmployeeOnboarding::query()
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', now()->startOfDay())
                    ->whereIn('status', [
                        OnboardingStatus::NotStarted->value,
                        OnboardingStatus::InProgress->value,
                    ])
                    ->with('employee.user')
                    ->orderBy('due_date')
            )
            ->columns([
                TextColumn::make('employee.user.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OnboardingStatus|string $state): string => ($state instanceof OnboardingStatus ? $state : OnboardingStatus::from($state))->label())
                    ->color(fn (OnboardingStatus|string $state): string => ($state instanceof OnboardingStatus ? $state : OnboardingStatus::from($state))->color()),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
            ])
            ->recordUrl(fn (EmployeeOnboarding $record): string => EmployeeOnboardingResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10]);
    }
}
