<?php

namespace App\Observers;

use App\Enums\AuditActionType;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class InvoiceObserver
{
    use LogsDeletion;

    public function updated(Invoice $invoice): void
    {
        if ($invoice->wasChanged('status')) {
            AuditLogger::record(
                $invoice,
                AuditActionType::StatusChange,
                $invoice->getOriginal(),
                $invoice->getAttributes()
            );

            if ($invoice->status === InvoiceStatus::Cancelled) {
                AuditLogger::record(
                    $invoice,
                    AuditActionType::FinancialAction,
                    $invoice->getOriginal(),
                    $invoice->getAttributes(),
                    (string) $invoice->getOriginal('total_amount'),
                    (string) $invoice->total_amount
                );
            }
        }

        if ($invoice->wasChanged('total_amount')) {
            AuditLogger::record(
                $invoice,
                AuditActionType::FinancialAction,
                $invoice->getOriginal(),
                $invoice->getAttributes(),
                (string) $invoice->getOriginal('total_amount'),
                (string) $invoice->total_amount
            );
        }
    }
}
