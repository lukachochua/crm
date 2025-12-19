<?php

namespace App\Filament\Exports;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class PaymentExporter extends Exporter
{
    protected static ?string $model = Payment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('invoice_id'),
            ExportColumn::make('invoice.invoice_number')
                ->label('Invoice Number'),
            ExportColumn::make('amount'),
            ExportColumn::make('status')
                ->formatStateUsing(fn (PaymentStatus|string $state): string => ($state instanceof PaymentStatus ? $state : PaymentStatus::from($state))->label()),
            ExportColumn::make('payment_date'),
            ExportColumn::make('created_by'),
            ExportColumn::make('payment_method'),
            ExportColumn::make('reference_number'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['invoice']);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your payment export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
