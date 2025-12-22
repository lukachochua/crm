<?php

namespace App\Filament\Resources\Hr;

use App\Enums\Hr\KpiCycleStatus;
use App\Enums\Hr\PeriodType;
use App\Filament\Resources\Hr\KpiCycleResource\Pages;
use App\Models\Hr\Kpi\KpiCycle;
use Filament\Forms\Components\DatePicker;
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

class KpiCycleResource extends Resource
{
    protected static ?string $model = KpiCycle::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'HR';

    protected static ?int $navigationSort = 41;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('period_type')
                    ->options(static::periodTypeOptions())
                    ->required(),
                DatePicker::make('period_start')
                    ->required(),
                DatePicker::make('period_end')
                    ->required(),
                TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('period_type')
                    ->formatStateUsing(fn (PeriodType|string $state): string => ($state instanceof PeriodType ? $state : PeriodType::from($state))->label())
                    ->sortable(),
                TextColumn::make('period_start')
                    ->date()
                    ->sortable(),
                TextColumn::make('period_end')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (KpiCycleStatus|string $state): string => ($state instanceof KpiCycleStatus ? $state : KpiCycleStatus::from($state))->label())
                    ->color(fn (KpiCycleStatus|string $state): string => ($state instanceof KpiCycleStatus ? $state : KpiCycleStatus::from($state))->color())
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (KpiCycle $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (KpiCycle $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new KpiCycle())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKpiCycles::route('/'),
            'create' => Pages\CreateKpiCycle::route('/create'),
            'view' => Pages\ViewKpiCycle::route('/{record}'),
            'edit' => Pages\EditKpiCycle::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (KpiCycleStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }

    protected static function periodTypeOptions(): array
    {
        $options = [];

        foreach (PeriodType::cases() as $type) {
            $options[$type->value] = $type->label();
        }

        return $options;
    }
}
