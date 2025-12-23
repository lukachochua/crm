<?php

namespace App\Filament\Resources\Crm\CustomerResource\RelationManagers;

use App\Enums\Crm\OrderStatus;
use App\Models\Crm\Sales\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Gate::allows('viewAny', Order::class);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('order_number')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->options($this->statusOptions())
                    ->required(),
                TextInput::make('total_amount')
                    ->numeric()
                    ->required(),
                TextInput::make('discount_amount')
                    ->numeric()
                    ->nullable(),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus|string $state): string => ($state instanceof OrderStatus ? $state : OrderStatus::from($state))->label())
                    ->color(fn (OrderStatus|string $state): string => ($state instanceof OrderStatus ? $state : OrderStatus::from($state))->color())
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options($this->statusOptions()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn (): bool => Gate::allows('create', Order::class)),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Order $record): bool => Gate::allows('update', $record)),
                DeleteAction::make()
                    ->visible(fn (Order $record): bool => Gate::allows('delete', $record)),
            ]);
    }

    private function statusOptions(): array
    {
        $options = [];

        foreach (OrderStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
