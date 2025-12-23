<?php

namespace App\Filament\Widgets\Crm;

use App\Enums\OrderStatus;
use App\Filament\Resources\Crm\OrderResource;
use App\Models\Crm\Order;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;

class RecentOrdersWidget extends TableWidget
{
    protected static ?int $sort = -14;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Orders')
            ->query(
                Order::query()
                    ->with('customer')
                    ->latest('created_at')
            )
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order')
                    ->searchable(),
                TextColumn::make('customer.last_name')
                    ->label('Customer')
                    ->formatStateUsing(fn (Order $record): string => $record->customer
                        ? $record->customer->first_name . ' ' . $record->customer->last_name
                        : '-'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus|string $state): string => ($state instanceof OrderStatus ? $state : OrderStatus::from($state))->label())
                    ->color(fn (OrderStatus|string $state): string => ($state instanceof OrderStatus ? $state : OrderStatus::from($state))->color()),
                TextColumn::make('total_amount')
                    ->numeric(decimalPlaces: 2)
                    ->label('Total'),
            ])
            ->recordUrl(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10]);
    }
}
