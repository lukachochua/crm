<?php

namespace App\Filament\Exports\Crm;

use App\Enums\Crm\InvoiceStatus;
use App\Models\Crm\Billing\Invoice;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class InvoiceExporter extends Exporter
{
    protected static ?string $model = Invoice::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('invoice_number'),
            ExportColumn::make('order_id'),
            ExportColumn::make('order.order_number')
                ->label('Order Number'),
            ExportColumn::make('status')
                ->formatStateUsing(fn (InvoiceStatus|string $state): string => ($state instanceof InvoiceStatus ? $state : InvoiceStatus::from($state))->label()),
            ExportColumn::make('total_amount'),
            ExportColumn::make('issued_at'),
            ExportColumn::make('due_date'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['order']);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your invoice export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
