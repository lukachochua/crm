<?php

namespace App\Filament\Resources\Hr\EmployeeResource\RelationManagers;

use App\Enums\Hr\KpiReportStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KpiReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'kpiReports';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kpi_template_id')
                    ->label('Template')
                    ->relationship('template', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('kpi_cycle_id')
                    ->label('Cycle')
                    ->relationship('cycle', 'label')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
                DateTimePicker::make('self_submitted_at')
                    ->nullable(),
                DateTimePicker::make('manager_reviewed_at')
                    ->nullable(),
                TextInput::make('self_score_total')
                    ->numeric()
                    ->nullable(),
                TextInput::make('manager_score_total')
                    ->numeric()
                    ->nullable(),
                TextInput::make('computed_score')
                    ->numeric()
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('template.name')
                    ->label('Template')
                    ->sortable(),
                TextColumn::make('cycle.label')
                    ->label('Cycle')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (KpiReportStatus|string $state): string => ($state instanceof KpiReportStatus ? $state : KpiReportStatus::from($state))->label())
                    ->color(fn (KpiReportStatus|string $state): string => ($state instanceof KpiReportStatus ? $state : KpiReportStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('computed_score')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (KpiReportStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
