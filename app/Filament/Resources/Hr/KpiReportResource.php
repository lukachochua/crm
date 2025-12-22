<?php

namespace App\Filament\Resources\Hr;

use App\Enums\Hr\KpiReportStatus;
use App\Filament\Resources\Hr\KpiReportResource\Pages;
use App\Filament\Resources\Hr\KpiReportResource\RelationManagers\KpiReportItemsRelationManager;
use App\Models\Hr\Employee;
use App\Models\Hr\Kpi\KpiReport;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class KpiReportResource extends Resource
{
    protected static ?string $model = KpiReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'HR';

    protected static ?int $navigationSort = 42;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Employee $record): string => $record->user?->name ?? ('Employee #' . $record->id))
                    ->searchable()
                    ->preload()
                    ->required(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.user.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
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
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (KpiReport $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (KpiReport $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new KpiReport())),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            KpiReportItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKpiReports::route('/'),
            'create' => Pages\CreateKpiReport::route('/create'),
            'view' => Pages\ViewKpiReport::route('/{record}'),
            'edit' => Pages\EditKpiReport::route('/{record}/edit'),
        ];
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
