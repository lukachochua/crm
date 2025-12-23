<?php

namespace App\Observers\Crm;

use App\Enums\AuditActionType;
use App\Enums\Crm\InvoiceStatus;
use App\Enums\Crm\OrderStatus;
use App\Enums\Crm\PaymentStatus;
use App\Models\Crm\Billing\Invoice;
use App\Models\Crm\Sales\Order;
use App\Models\Crm\Billing\Payment;
use App\Observers\Concerns\LogsDeletion;
use App\Services\AuditLogger;
use Filament\Notifications\Notification;

class OrderObserver
{
    use LogsDeletion;

    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        AuditLogger::record(
            $order,
            AuditActionType::StatusChange,
            $order->getOriginal(),
            $order->getAttributes()
        );

        $this->maybeCreateInvoice($order);
    }

    private function maybeCreateInvoice(Order $order): void
    {
        $status = $order->status instanceof OrderStatus
            ? $order->status
            : OrderStatus::from($order->status);

        if ($status !== OrderStatus::Completed) {
            return;
        }

        $billableTotal = $this->billableTotalForOrder($order);
        $totalPaid = $this->totalPaidForOrder($order);
        $hasInvoices = $order->invoices()->exists();

        if ($hasInvoices && $totalPaid >= $billableTotal) {
            $this->notifyWarning('Order is fully paid and already invoiced; skipped auto-invoice.');
            return;
        }

        $totalInvoiced = (float) $order->invoices()->sum('total_amount');
        $remaining = round($billableTotal - $totalInvoiced, 2);

        if ($remaining <= 0) {
            $this->notifyWarning('No remaining amount to invoice for this order.');
            return;
        }

        Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => $this->generateInvoiceNumber(now()),
            'status' => InvoiceStatus::Draft->value,
            'total_amount' => $remaining,
            'issued_at' => now(),
            'due_date' => null,
            'notes' => 'Auto-created when order completed.',
        ]);
    }

    private function billableTotalForOrder(Order $order): float
    {
        $total = (float) $order->total_amount;
        $discount = (float) ($order->discount_amount ?? 0);

        return max(0, $total - $discount);
    }

    private function totalPaidForOrder(Order $order): float
    {
        $completed = (float) Payment::query()
            ->whereHas('invoice', fn ($query) => $query->where('order_id', $order->id))
            ->where('status', PaymentStatus::Completed->value)
            ->sum('amount');

        $reversed = (float) Payment::query()
            ->whereHas('invoice', fn ($query) => $query->where('order_id', $order->id))
            ->where('status', PaymentStatus::Reversed->value)
            ->sum('amount');

        return $completed - $reversed;
    }

    private function generateInvoiceNumber($issuedAt): string
    {
        $prefix = $issuedAt->format('Ymd');

        do {
            $number = sprintf('INV-%s-%04d', $prefix, random_int(1, 9999));
        } while (Invoice::where('invoice_number', $number)->exists());

        return $number;
    }

    private function notifyWarning(string $message): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        Notification::make()
            ->warning()
            ->title($message)
            ->send();
    }
}
