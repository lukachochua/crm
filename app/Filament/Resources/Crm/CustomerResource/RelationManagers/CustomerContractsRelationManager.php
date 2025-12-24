<?php

namespace App\Filament\Resources\Crm\CustomerResource\RelationManagers;

use App\Enums\Crm\ContractStatus;
use App\Models\Crm\Parties\CustomerContract;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class CustomerContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'contracts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('contract_number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('contract_type')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->nullable(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contract_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contract_type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ContractStatus|string $state): string => ($state instanceof ContractStatus ? $state : ContractStatus::from($state))->label())
                    ->color(fn (ContractStatus|string $state): string => ($state instanceof ContractStatus ? $state : ContractStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn (): bool => Gate::allows('create', CustomerContract::class)),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (CustomerContract $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (CustomerContract $record): bool => Gate::allows('delete', $record)),
            ]);
    }

    protected static function statusOptions(): array
    {
        $options = [];

        foreach (ContractStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
