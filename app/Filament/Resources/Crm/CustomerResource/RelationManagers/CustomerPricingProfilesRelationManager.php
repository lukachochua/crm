<?php

namespace App\Filament\Resources\Crm\CustomerResource\RelationManagers;

use App\Models\Crm\Parties\CustomerPricingProfile;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class CustomerPricingProfilesRelationManager extends RelationManager
{
    protected static string $relationship = 'pricingProfiles';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('pricing_type')
                    ->required()
                    ->maxLength(255),
                TextInput::make('discount_percent')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->nullable(),
                TextInput::make('currency_code')
                    ->required()
                    ->maxLength(3),
                Toggle::make('is_active')
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
                TextColumn::make('pricing_type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('discount_percent')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('currency_code')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn (): bool => Gate::allows('create', CustomerPricingProfile::class)),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (CustomerPricingProfile $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (CustomerPricingProfile $record): bool => Gate::allows('delete', $record)),
            ]);
    }
}
