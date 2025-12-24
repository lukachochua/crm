<?php

namespace App\Services\Crm;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DocumentRegistryQuery
{
    public function forUser(?User $user): Builder
    {
        if (! $user) {
            return $this->emptyQuery();
        }

        $queries = [];

        if ($user->can(Permissions::permission('applications', 'view'))) {
            $queries[] = $this->applicationsQuery();
        }

        if ($user->can(Permissions::permission('orders', 'view'))) {
            $queries[] = $this->ordersQuery();
        }

        if ($user->can(Permissions::permission('reservations', 'view'))) {
            $queries[] = $this->reservationsQuery();
        }

        if ($user->can(Permissions::permission('invoices', 'view'))) {
            $queries[] = $this->invoicesQuery();
        }

        if ($user->can(Permissions::permission('payments', 'view'))) {
            $queries[] = $this->paymentsQuery();
        }

        if ($queries === []) {
            return $this->emptyQuery();
        }

        $query = array_shift($queries);

        foreach ($queries as $next) {
            $query->unionAll($next);
        }

        return $query;
    }

    private function applicationsQuery(): Builder
    {
        return DB::table('applications')
            ->leftJoin('customers', 'customers.id', '=', 'applications.customer_id')
            ->leftJoin('users', 'users.id', '=', 'applications.created_by')
            ->whereNull('applications.deleted_at')
            ->selectRaw("'application' as document_type")
            ->selectRaw("concat('application:', applications.id) as document_key")
            ->selectRaw('applications.id as document_id')
            ->selectRaw("concat('APP-', applications.id) as reference")
            ->selectRaw($this->customerNameExpression() . ' as related_customer')
            ->selectRaw('applications.status as status')
            ->selectRaw('applications.created_at as created_at')
            ->selectRaw('applications.created_by as created_by')
            ->selectRaw('users.name as created_by_name');
    }

    private function ordersQuery(): Builder
    {
        return DB::table('orders')
            ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
            ->leftJoin('users', 'users.id', '=', 'orders.created_by')
            ->whereNull('orders.deleted_at')
            ->selectRaw("'order' as document_type")
            ->selectRaw("concat('order:', orders.id) as document_key")
            ->selectRaw('orders.id as document_id')
            ->selectRaw("coalesce(orders.order_number, concat('ORD-', orders.id)) as reference")
            ->selectRaw($this->customerNameExpression() . ' as related_customer')
            ->selectRaw('orders.status as status')
            ->selectRaw('orders.created_at as created_at')
            ->selectRaw('orders.created_by as created_by')
            ->selectRaw('users.name as created_by_name');
    }

    private function reservationsQuery(): Builder
    {
        return DB::table('reservations')
            ->leftJoin('orders', 'orders.id', '=', 'reservations.order_id')
            ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
            ->whereNull('reservations.deleted_at')
            ->selectRaw("'reservation' as document_type")
            ->selectRaw("concat('reservation:', reservations.id) as document_key")
            ->selectRaw('reservations.id as document_id')
            ->selectRaw("concat('RSV-', reservations.id) as reference")
            ->selectRaw($this->customerNameExpression() . ' as related_customer')
            ->selectRaw('reservations.status as status')
            ->selectRaw('reservations.created_at as created_at')
            ->selectRaw('null as created_by')
            ->selectRaw('null as created_by_name');
    }

    private function invoicesQuery(): Builder
    {
        return DB::table('invoices')
            ->leftJoin('orders', 'orders.id', '=', 'invoices.order_id')
            ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
            ->whereNull('invoices.deleted_at')
            ->selectRaw("'invoice' as document_type")
            ->selectRaw("concat('invoice:', invoices.id) as document_key")
            ->selectRaw('invoices.id as document_id')
            ->selectRaw("coalesce(invoices.invoice_number, concat('INV-', invoices.id)) as reference")
            ->selectRaw($this->customerNameExpression() . ' as related_customer')
            ->selectRaw('invoices.status as status')
            ->selectRaw('invoices.created_at as created_at')
            ->selectRaw('null as created_by')
            ->selectRaw('null as created_by_name');
    }

    private function paymentsQuery(): Builder
    {
        return DB::table('payments')
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('orders', 'orders.id', '=', 'invoices.order_id')
            ->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
            ->leftJoin('users', 'users.id', '=', 'payments.created_by')
            ->whereNull('payments.deleted_at')
            ->selectRaw("'payment' as document_type")
            ->selectRaw("concat('payment:', payments.id) as document_key")
            ->selectRaw('payments.id as document_id')
            ->selectRaw("coalesce(payments.reference_number, concat('PAY-', payments.id)) as reference")
            ->selectRaw($this->customerNameExpression() . ' as related_customer')
            ->selectRaw('payments.status as status')
            ->selectRaw('payments.created_at as created_at')
            ->selectRaw('payments.created_by as created_by')
            ->selectRaw('users.name as created_by_name');
    }

    private function emptyQuery(): Builder
    {
        return DB::query()
            ->selectRaw("null as document_type")
            ->selectRaw("null as document_key")
            ->selectRaw('null as document_id')
            ->selectRaw('null as reference')
            ->selectRaw('null as related_customer')
            ->selectRaw('null as status')
            ->selectRaw('null as created_at')
            ->selectRaw('null as created_by')
            ->selectRaw('null as created_by_name')
            ->whereRaw('1 = 0');
    }

    private function customerNameExpression(): string
    {
        return "trim(concat(coalesce(customers.first_name, ''), ' ', coalesce(customers.last_name, '')))";
    }
}
