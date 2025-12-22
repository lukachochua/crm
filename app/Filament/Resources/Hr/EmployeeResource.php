<?php

namespace App\Filament\Resources\Hr;

use App\Enums\Hr\EmployeeStatus;
use App\Filament\Resources\Hr\EmployeeResource\Pages;
use App\Filament\Resources\Hr\EmployeeResource\RelationManagers\EmployeeDocumentsRelationManager;
use App\Filament\Resources\Hr\EmployeeResource\RelationManagers\KpiReportsRelationManager;
use App\Models\Hr\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'HR';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('position_id')
                    ->label('Position')
                    ->relationship('position', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('branch_id')
                    ->label('Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('contract_type_id')
                    ->label('Contract Type')
                    ->relationship('contractType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('manager_user_id')
                    ->label('Manager')
                    ->relationship('manager', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('contract_end_date')
                    ->nullable(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable(),
                TextColumn::make('position.name')
                    ->label('Position')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (EmployeeStatus|string $state): string => ($state instanceof EmployeeStatus ? $state : EmployeeStatus::from($state))->label())
                    ->color(fn (EmployeeStatus|string $state): string => ($state instanceof EmployeeStatus ? $state : EmployeeStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('contract_end_date')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Employee $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (Employee $record): bool => Gate::allows('delete', $record)),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('delete', new Employee())),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EmployeeDocumentsRelationManager::class,
            KpiReportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (EmployeeStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
