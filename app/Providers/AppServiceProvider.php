<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Observers\ApplicationObserver;
use App\Observers\CustomerObserver;
use App\Observers\InvoiceObserver;
use App\Observers\OrderObserver;
use App\Observers\PaymentObserver;
use App\Observers\ReservationObserver;
use App\Observers\VehicleObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('local') && ! $this->app->runningInConsole()) {
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        }

        Application::observe(ApplicationObserver::class);
        Order::observe(OrderObserver::class);
        Reservation::observe(ReservationObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Payment::observe(PaymentObserver::class);
        Customer::observe(CustomerObserver::class);
        Vehicle::observe(VehicleObserver::class);
    }
}
