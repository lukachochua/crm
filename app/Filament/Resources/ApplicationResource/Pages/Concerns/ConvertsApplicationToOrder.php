<?php

namespace App\Filament\Resources\ApplicationResource\Pages\Concerns;

use App\Enums\ApplicationStatus;
use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Application;
use App\Models\Order;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

trait ConvertsApplicationToOrder
{
    protected function convertToOrderAction(): Action
    {
        return Action::make('convertToOrder')
            ->label('Convert to Order')
            ->icon('heroicon-o-arrow-right-circle')
            ->color('success')
            ->visible(fn (): bool => $this->canShowConvertAction())
            ->disabled(fn (): bool => $this->conversionBlockReason() !== null)
            ->tooltip(fn (): ?string => $this->conversionBlockReason())
            ->requiresConfirmation()
            ->action(function (): void {
                $order = $this->convertToOrder();

                if ($order) {
                    $resource = OrderResource::class;
                    $target = $resource::canEdit($order)
                        ? $resource::getUrl('edit', ['record' => $order])
                        : $resource::getUrl('view', ['record' => $order]);

                    $this->redirect($target);
                }
            })
            ->successNotificationTitle('Order created');
    }

    private function canShowConvertAction(): bool
    {
        $application = $this->getRecord();

        return Gate::allows('create', Order::class)
            && Gate::allows('update', $application);
    }

    private function conversionBlockReason(): ?string
    {
        /** @var Application $application */
        $application = $this->getRecord();

        if ($application->order()->exists()) {
            return 'Order already exists for this application.';
        }

        $status = $application->status instanceof ApplicationStatus
            ? $application->status
            : ApplicationStatus::from($application->status);

        if ($status !== ApplicationStatus::Approved) {
            return 'Application must be approved before conversion.';
        }

        if (! Auth::id()) {
            return 'You must be signed in to convert.';
        }

        return null;
    }

    private function convertToOrder(): ?Order
    {
        /** @var Application $application */
        $application = $this->getRecord();

        if ($this->conversionBlockReason() !== null) {
            return null;
        }

        $order = null;

        DB::transaction(function () use ($application, &$order): void {
            $order = Order::create([
                'customer_id' => $application->customer_id,
                'application_id' => $application->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => OrderStatus::Draft->value,
                'total_amount' => 0,
                'discount_amount' => null,
                'notes' => 'Auto-created from Application #' . $application->id,
                'created_by' => Auth::id(),
            ]);

            $application->status = ApplicationStatus::Converted;
            $application->save();
        });

        return $order;
    }

    private function generateOrderNumber(): string
    {
        $prefix = now()->format('Ymd');

        do {
            $number = sprintf('ORD-%s-%04d', $prefix, random_int(1, 9999));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
