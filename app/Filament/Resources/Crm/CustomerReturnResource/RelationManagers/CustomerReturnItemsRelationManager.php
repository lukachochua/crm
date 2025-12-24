<?php

namespace App\Filament\Resources\Crm\CustomerReturnResource\RelationManagers;

use App\Models\Crm\Operations\CustomerReturn;
use App\Models\Crm\Operations\CustomerReturnItem;
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

class CustomerReturnItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('item_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(1)
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
                TextColumn::make('item_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn (): bool => $this->canManageItems()),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (CustomerReturnItem $record): bool => $this->canManageItems()),
                DeleteAction::make()
                    ->visible(fn (CustomerReturnItem $record): bool => $this->canManageItems()),
            ]);
    }

    private function canManageItems(): bool
    {
        /** @var CustomerReturn $return */
        $return = $this->getOwnerRecord();

        return Gate::allows('update', $return) && ! $return->isClosedOrCancelled();
    }
}
