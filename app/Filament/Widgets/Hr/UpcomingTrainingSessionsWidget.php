<?php

namespace App\Filament\Widgets\Hr;

use App\Enums\Hr\TrainingSessionStatus;
use App\Filament\Resources\Hr\TrainingSessionResource;
use App\Models\Hr\Training\TrainingSession;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UpcomingTrainingSessionsWidget extends TableWidget
{
    protected static ?int $sort = -10;

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
            ->heading('Upcoming Training Sessions')
            ->query(
                TrainingSession::query()
                    ->where('status', TrainingSessionStatus::Scheduled->value)
                    ->where('starts_at', '>=', now())
                    ->withCount('participants')
                    ->orderBy('starts_at')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Session')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('location')
                    ->toggleable(),
                TextColumn::make('participants_count')
                    ->label('Participants')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordUrl(fn (TrainingSession $record): string => TrainingSessionResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10]);
    }
}
