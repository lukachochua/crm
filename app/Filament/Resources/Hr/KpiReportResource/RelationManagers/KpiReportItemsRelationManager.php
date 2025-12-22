<?php

namespace App\Filament\Resources\Hr\KpiReportResource\RelationManagers;

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

class KpiReportItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kpi_template_item_id')
                    ->label('Template Item')
                    ->relationship('templateItem', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('self_score')
                    ->numeric()
                    ->nullable(),
                TextInput::make('manager_score')
                    ->numeric()
                    ->nullable(),
                TextInput::make('computed_score')
                    ->numeric()
                    ->disabled(),
                Textarea::make('self_comment')
                    ->columnSpanFull()
                    ->rows(2),
                Textarea::make('manager_comment')
                    ->columnSpanFull()
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('templateItem.title')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('self_score')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('manager_score')
                    ->numeric(decimalPlaces: 2)
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
}
