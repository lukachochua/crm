<?php

namespace App\Filament\Resources\Hr;

use App\Enums\Hr\OnboardingStatus;
use App\Filament\Resources\Hr\EmployeeOnboardingResource\Pages;
use App\Filament\Resources\Hr\EmployeeOnboardingResource\RelationManagers\EmployeeOnboardingTasksRelationManager;
use App\Models\Hr\Employee;
use App\Models\Hr\Onboarding\EmployeeOnboarding;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class EmployeeOnboardingResource extends HrResource
{
    protected static ?string $model = EmployeeOnboarding::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $hrNavigationGroup = 'Onboarding';

    protected static ?int $navigationSort = 71;

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
                Select::make('onboarding_template_id')
                    ->label('Template')
                    ->relationship('template', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('due_date')
                    ->nullable(),
                DateTimePicker::make('completed_at')
                    ->nullable(),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
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
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OnboardingStatus|string $state): string => ($state instanceof OnboardingStatus ? $state : OnboardingStatus::from($state))->label())
                    ->color(fn (OnboardingStatus|string $state): string => ($state instanceof OnboardingStatus ? $state : OnboardingStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (EmployeeOnboarding $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (EmployeeOnboarding $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new EmployeeOnboarding())),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EmployeeOnboardingTasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeOnboardings::route('/'),
            'create' => Pages\CreateEmployeeOnboarding::route('/create'),
            'view' => Pages\ViewEmployeeOnboarding::route('/{record}'),
            'edit' => Pages\EditEmployeeOnboarding::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (OnboardingStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
