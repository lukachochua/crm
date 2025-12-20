<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS turnover_overviews');

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            $invoicePeriod = "to_char(issued_at, 'YYYY-MM')";
            $paymentPeriod = "to_char(payment_date, 'YYYY-MM')";
        } else {
            $invoicePeriod = "DATE_FORMAT(issued_at, '%Y-%m')";
            $paymentPeriod = "DATE_FORMAT(payment_date, '%Y-%m')";
        }

        DB::statement("
            CREATE VIEW turnover_overviews AS
            SELECT period,
                   SUM(total_invoiced) AS total_invoiced,
                   SUM(total_paid) AS total_paid,
                   SUM(total_invoiced) - SUM(total_paid) AS outstanding_amount
            FROM (
                SELECT {$invoicePeriod} AS period,
                       SUM(total_amount) AS total_invoiced,
                       0 AS total_paid
                FROM invoices
                WHERE deleted_at IS NULL
                GROUP BY {$invoicePeriod}
                UNION ALL
                SELECT {$paymentPeriod} AS period,
                       0 AS total_invoiced,
                       SUM(amount) AS total_paid
                FROM payments
                WHERE deleted_at IS NULL
                GROUP BY {$paymentPeriod}
            ) AS aggregates
            GROUP BY period
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS turnover_overviews');
    }
};
