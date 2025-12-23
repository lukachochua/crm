<?php

namespace App\Filament\Widgets\Crm;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\Crm\InvoiceResource;
use App\Models\Crm\Invoice;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;

class OverdueInvoicesWidget extends TableWidget
{
    protected static ?int $sort = -13;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Overdue Invoices')
            ->query(
                Invoice::query()
                    ->with(['order.customer'])
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', now())
                    ->whereIn('status', [
                        InvoiceStatus::Issued->value,
                        InvoiceStatus::PartiallyPaid->value,
                    ])
                    ->orderBy('due_date')
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice'),
                TextColumn::make('order.order_number')
                    ->label('Order'),
                TextColumn::make('order.customer.last_name')
                    ->label('Customer')
                    ->formatStateUsing(fn (Invoice $record): string => $record->order && $record->order->customer
                        ? $record->order->customer->first_name . ' ' . $record->order->customer->last_name
                        : '-'),
                TextColumn::make('due_date')
                    ->dateTime()
                    ->label('Due'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (InvoiceStatus|string $state): string => ($state instanceof InvoiceStatus ? $state : InvoiceStatus::from($state))->label())
                    ->color(fn (InvoiceStatus|string $state): string => ($state instanceof InvoiceStatus ? $state : InvoiceStatus::from($state))->color()),
            ])
            ->recordUrl(fn (Invoice $record): string => InvoiceResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10]);
    }
}
