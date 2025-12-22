<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\ApplicationResource;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\TurnoverOverviewResource;
use App\Filament\Resources\VehicleResource;
use App\Models\Application;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CrmStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -30;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'CRM Overview';

    protected ?string $description = 'Key totals across customers, sales, and finance.';

    protected function getStats(): array
    {
        $customers = Customer::count();
        $newCustomers = Customer::where('created_at', '>=', now()->subDays(30))->count();

        $openApplications = Application::whereIn('status', [
            ApplicationStatus::NewRequest->value,
            ApplicationStatus::Reviewed->value,
        ])->count();

        $activeOrders = Order::where('status', OrderStatus::Confirmed->value)->count();

        $openInvoices = Invoice::whereIn('status', [
            InvoiceStatus::Issued->value,
            InvoiceStatus::PartiallyPaid->value,
        ])->count();

        $invoicedTotal = (float) Invoice::whereIn('status', [
            InvoiceStatus::Issued->value,
            InvoiceStatus::PartiallyPaid->value,
            InvoiceStatus::Paid->value,
        ])->sum('total_amount');

        $paidTotal = (float) Payment::where('status', PaymentStatus::Completed->value)->sum('amount');

        $outstandingTotal = max(0, $invoicedTotal - $paidTotal);

        $availableVehicles = Vehicle::where('status', 'available')->count();
        $reservedVehicles = Vehicle::where('status', 'reserved')->count();

        return [
            Stat::make('Customers', number_format($customers))
                ->description($newCustomers . ' added in last 30 days')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary')
                ->url(CustomerResource::getUrl()),
            Stat::make('Open Applications', number_format($openApplications))
                ->description('New + reviewed')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning')
                ->url(ApplicationResource::getUrl()),
            Stat::make('Active Orders', number_format($activeOrders))
                ->description('Confirmed orders')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info')
                ->url(OrderResource::getUrl()),
            Stat::make('Open Invoices', number_format($openInvoices))
                ->description('Issued or partially paid')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('danger')
                ->url(InvoiceResource::getUrl()),
            Stat::make('Outstanding Amount', number_format($outstandingTotal, 2))
                ->description('Total invoiced minus paid')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->url(TurnoverOverviewResource::getUrl()),
            Stat::make('Available Vehicles', number_format($availableVehicles))
                ->description($reservedVehicles . ' reserved')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary')
                ->url(VehicleResource::getUrl()),
        ];
    }
}
