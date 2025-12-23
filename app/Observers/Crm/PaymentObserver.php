<?php

namespace App\Observers\Crm;

use App\Enums\AuditActionType;
use App\Enums\Crm\PaymentStatus;
use App\Models\Crm\Billing\Payment;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;

class PaymentObserver
{
    use LogsDeletion;

    public function created(Payment $payment): void
    {
        AuditLogger::record(
            $payment,
            AuditActionType::FinancialAction,
            [],
            $payment->getAttributes(),
            null,
            (string) $payment->amount
        );
    }

    public function updated(Payment $payment): void
    {
        if ($payment->wasChanged('status')) {
            AuditLogger::record(
                $payment,
                AuditActionType::StatusChange,
                $payment->getOriginal(),
                $payment->getAttributes()
            );

            if (in_array($payment->status, [PaymentStatus::Completed, PaymentStatus::Reversed], true)) {
                AuditLogger::record(
                    $payment,
                    AuditActionType::FinancialAction,
                    $payment->getOriginal(),
                    $payment->getAttributes(),
                    (string) $payment->amount,
                    (string) $payment->amount
                );
            }
        }
    }
}
