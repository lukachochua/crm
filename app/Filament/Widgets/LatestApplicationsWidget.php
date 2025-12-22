<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;

class LatestApplicationsWidget extends TableWidget
{
    protected static ?int $sort = -15;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Applications')
            ->query(
                Application::query()
                    ->with('customer')
                    ->latest('requested_at')
            )
            ->columns([
                TextColumn::make('customer.last_name')
                    ->label('Customer')
                    ->formatStateUsing(fn (Application $record): string => $record->customer
                        ? $record->customer->first_name . ' ' . $record->customer->last_name
                        : '-'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ApplicationStatus|string $state): string => ($state instanceof ApplicationStatus ? $state : ApplicationStatus::from($state))->label())
                    ->color(fn (ApplicationStatus|string $state): string => ($state instanceof ApplicationStatus ? $state : ApplicationStatus::from($state))->color()),
                TextColumn::make('requested_at')
                    ->dateTime()
                    ->label('Requested'),
            ])
            ->recordUrl(fn (Application $record): string => ApplicationResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10]);
    }
}
