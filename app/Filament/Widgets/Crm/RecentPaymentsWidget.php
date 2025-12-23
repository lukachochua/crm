<?php

namespace App\Filament\Widgets\Crm;

use App\Enums\Crm\PaymentStatus;
use App\Filament\Resources\Crm\PaymentResource;
use App\Models\Crm\Billing\Payment;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;

class RecentPaymentsWidget extends TableWidget
{
    protected static ?int $sort = -11;

    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Payments')
            ->query(
                Payment::query()
                    ->with('invoice')
                    ->latest('payment_date')
            )
            ->columns([
                TextColumn::make('invoice.invoice_number')
                    ->label('Invoice'),
                TextColumn::make('amount')
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (PaymentStatus|string $state): string => ($state instanceof PaymentStatus ? $state : PaymentStatus::from($state))->label())
                    ->color(fn (PaymentStatus|string $state): string => ($state instanceof PaymentStatus ? $state : PaymentStatus::from($state))->color()),
                TextColumn::make('payment_date')
                    ->dateTime()
                    ->label('Paid At'),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : '-'),
            ])
            ->recordUrl(fn (Payment $record): string => PaymentResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10]);
    }
}
