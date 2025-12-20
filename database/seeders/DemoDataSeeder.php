<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Models\Application;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        $users = $this->seedUsers();
        $creatorIds = collect([
            $users['admin']->id,
            $users['sales']->id,
            $users['backoffice']->id,
        ]);

        $customers = $this->seedCustomers(12);
        $vehicles = $this->seedVehicles(18);
        $applications = $this->seedApplications($customers, $creatorIds);
        $orders = $this->seedOrders($customers, $applications, $creatorIds);
        $this->seedReservations($orders, $vehicles);
        $invoices = $this->seedInvoices($orders);

        Auth::login($users['finance']);
        $this->seedPayments($invoices, $users['finance']->id);
        Auth::logout();

        Model::reguard();
    }

    private function seedUsers(): array
    {
        $users = [];

        $users['admin'] = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $users['admin']->assignRole('Admin');

        $users['sales'] = User::factory()->create([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => Hash::make('password'),
        ]);
        $users['sales']->assignRole('Sales');

        $users['backoffice'] = User::factory()->create([
            'name' => 'Back Office User',
            'email' => 'backoffice@example.com',
            'password' => Hash::make('password'),
        ]);
        $users['backoffice']->assignRole('Back Office');

        $users['finance'] = User::factory()->create([
            'name' => 'Finance User',
            'email' => 'finance@example.com',
            'password' => Hash::make('password'),
        ]);
        $users['finance']->assignRole('Finance');

        $users['turnover'] = User::factory()->create([
            'name' => 'Turnover User',
            'email' => 'turnover@example.com',
            'password' => Hash::make('password'),
        ]);
        $users['turnover']->assignRole('Turnover');

        User::factory()->count(3)->create();

        return $users;
    }

    private function seedCustomers(int $count): Collection
    {
        $faker = fake();
        $customers = collect();

        for ($i = 1; $i <= $count; $i++) {
            $createdAt = Carbon::instance($faker->dateTimeBetween('-9 months', '-1 day'));

            $customers->push(Customer::create([
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'personal_id_or_tax_id' => sprintf('TAX-%04d', $i),
                'phone' => $faker->numerify('555-01##'),
                'email' => $faker->boolean(75) ? $faker->unique()->safeEmail() : null,
                'address' => $faker->boolean(70) ? $faker->streetAddress() : null,
                'notes' => $faker->boolean(30) ? $faker->sentence() : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]));
        }

        return $customers;
    }

    private function seedVehicles(int $count): Collection
    {
        $faker = fake();
        $vehicles = collect();
        $types = ['Sedan', 'SUV', 'Truck', 'Van', 'Motorcycle'];
        $models = ['Apex', 'Summit', 'Voyager', 'Nova', 'Trail'];
        $colors = ['Black', 'White', 'Silver', 'Blue', 'Red', 'Gray'];
        $statuses = ['available', 'reserved', 'sold'];

        for ($i = 1; $i <= $count; $i++) {
            $createdAt = Carbon::instance($faker->dateTimeBetween('-6 months', 'now'));

            $vehicles->push(Vehicle::create([
                'vin_or_serial' => sprintf('VIN-%05d', $i),
                'type' => $faker->randomElement($types),
                'status' => $statuses[($i - 1) % count($statuses)],
                'model' => $faker->randomElement($models),
                'year' => $faker->numberBetween(2015, 2024),
                'color' => $faker->randomElement($colors),
                'notes' => $faker->boolean(20) ? $faker->sentence() : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]));
        }

        return $vehicles;
    }

    private function seedApplications(Collection $customers, Collection $creatorIds): Collection
    {
        $faker = fake();
        $applications = collect();
        $sources = ['walk-in', 'phone', 'online'];
        $statuses = ApplicationStatus::cases();

        foreach ($statuses as $status) {
            for ($i = 0; $i < 2; $i++) {
                $requestedAt = Carbon::instance($faker->dateTimeBetween('-90 days', '-1 day'));
                $createdAt = $requestedAt->copy()->addHours($faker->numberBetween(1, 48));

                $applications->push(Application::create([
                    'customer_id' => $customers->random()->id,
                    'status' => $status->value,
                    'requested_at' => $requestedAt,
                    'created_by' => $creatorIds->random(),
                    'description' => $faker->boolean(70) ? $faker->sentence() : null,
                    'source' => $faker->boolean(80) ? $faker->randomElement($sources) : null,
                    'internal_notes' => $faker->boolean(35) ? $faker->sentence() : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]));
            }
        }

        for ($i = 0; $i < 3; $i++) {
            $status = $faker->randomElement($statuses);
            $requestedAt = Carbon::instance($faker->dateTimeBetween('-60 days', '-1 day'));
            $createdAt = $requestedAt->copy()->addHours($faker->numberBetween(1, 24));

            $applications->push(Application::create([
                'customer_id' => $customers->random()->id,
                'status' => $status->value,
                'requested_at' => $requestedAt,
                'created_by' => $creatorIds->random(),
                'description' => $faker->boolean(60) ? $faker->sentence() : null,
                'source' => $faker->boolean(80) ? $faker->randomElement($sources) : null,
                'internal_notes' => $faker->boolean(25) ? $faker->sentence() : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]));
        }

        return $applications;
    }

    private function seedOrders(Collection $customers, Collection $applications, Collection $creatorIds): Collection
    {
        $faker = fake();
        $orders = collect();
        $statuses = OrderStatus::cases();
        $orderNumber = 1001;
        $eligibleApplications = $applications
            ->filter(fn (Application $application): bool => in_array(
                $application->status,
                [ApplicationStatus::Approved, ApplicationStatus::Converted],
                true
            ))
            ->values();

        for ($i = 0; $i < 10; $i++) {
            $status = $statuses[$i % count($statuses)];
            $application = $eligibleApplications->shift();
            $customerId = $application ? $application->customer_id : $customers->random()->id;
            $total = $faker->randomFloat(2, 5000, 50000);
            $maxDiscount = max(100, $total / 3);
            $discount = $faker->boolean(40) ? $faker->randomFloat(2, 100, $maxDiscount) : null;
            $createdAt = Carbon::instance($faker->dateTimeBetween('-120 days', 'now'));

            $orders->push(Order::create([
                'customer_id' => $customerId,
                'application_id' => $application?->id,
                'order_number' => sprintf('ORD-%04d', $orderNumber),
                'status' => $status->value,
                'total_amount' => $total,
                'discount_amount' => $discount,
                'notes' => $faker->boolean(25) ? $faker->sentence() : null,
                'created_by' => $creatorIds->random(),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]));

            $orderNumber++;
        }

        return $orders;
    }

    private function seedReservations(Collection $orders, Collection $vehicles): void
    {
        $faker = fake();
        $statuses = ReservationStatus::cases();
        $vehiclesByStatus = [
            'available' => $vehicles->where('status', 'available')->values(),
            'reserved' => $vehicles->where('status', 'reserved')->values(),
            'sold' => $vehicles->where('status', 'sold')->values(),
        ];

        foreach ($orders->values() as $index => $order) {
            $status = $statuses[$index % count($statuses)];
            $vehicle = $this->vehicleForReservation($status, $vehiclesByStatus, $vehicles);
            [$reservedFrom, $reservedUntil] = $this->reservationWindow($status, $faker);
            $createdAt = $reservedFrom->copy()->subDays($faker->numberBetween(1, 3));

            Reservation::create([
                'order_id' => $order->id,
                'vehicle_id' => $vehicle->id,
                'status' => $status->value,
                'reserved_from' => $reservedFrom,
                'reserved_until' => $reservedUntil,
                'notes' => $faker->boolean(25) ? $faker->sentence() : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    private function seedInvoices(Collection $orders): Collection
    {
        $faker = fake();
        $invoices = collect();
        $statuses = InvoiceStatus::cases();
        $invoiceNumber = 2001;

        foreach ($orders->values() as $index => $order) {
            $status = $statuses[$index % count($statuses)];
            $issuedAt = Carbon::instance($faker->dateTimeBetween('-4 months', 'now'));
            $dueDate = $faker->boolean(85) ? $issuedAt->copy()->addDays(30) : null;
            $total = max(0.01, (float) $order->total_amount - (float) ($order->discount_amount ?? 0));

            $invoices->push(Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => sprintf('INV-%05d', $invoiceNumber),
                'status' => $status->value,
                'total_amount' => $total,
                'issued_at' => $issuedAt,
                'due_date' => $dueDate,
                'notes' => $faker->boolean(20) ? $faker->sentence() : null,
                'created_at' => $issuedAt,
                'updated_at' => $issuedAt,
            ]));

            $invoiceNumber++;
        }

        $extraOrder = $orders->first();
        if ($extraOrder) {
            $issuedAt = Carbon::instance($faker->dateTimeBetween('-2 months', 'now'));
            $invoices->push(Invoice::create([
                'order_id' => $extraOrder->id,
                'invoice_number' => sprintf('INV-%05d', $invoiceNumber),
                'status' => InvoiceStatus::Issued->value,
                'total_amount' => $faker->randomFloat(2, 500, 4000),
                'issued_at' => $issuedAt,
                'due_date' => $issuedAt->copy()->addDays(30),
                'notes' => 'Second invoice for split billing.',
                'created_at' => $issuedAt,
                'updated_at' => $issuedAt,
            ]));
        }

        return $invoices;
    }

    private function seedPayments(Collection $invoices, int $createdBy): void
    {
        $faker = fake();
        $addedReversed = false;

        foreach ($invoices as $invoice) {
            $status = $invoice->status instanceof InvoiceStatus
                ? $invoice->status
                : InvoiceStatus::from($invoice->status);
            $total = (float) $invoice->total_amount;

            if ($status === InvoiceStatus::Draft) {
                continue;
            }

            if ($status === InvoiceStatus::Issued) {
                $this->createPayment($invoice, $createdBy, $faker, $total * 0.2, PaymentStatus::Pending);
                continue;
            }

            if ($status === InvoiceStatus::PartiallyPaid) {
                $this->createPayment($invoice, $createdBy, $faker, $total * 0.4, PaymentStatus::Completed);
                continue;
            }

            if ($status === InvoiceStatus::Paid) {
                $this->createPayment($invoice, $createdBy, $faker, $total, PaymentStatus::Completed);

                if (! $addedReversed) {
                    $this->createPayment($invoice, $createdBy, $faker, max(1, $total * 0.1), PaymentStatus::Reversed);
                    $addedReversed = true;
                }

                continue;
            }

            $this->createPayment($invoice, $createdBy, $faker, max(1, $total * 0.1), PaymentStatus::Failed);
        }
    }

    private function createPayment(
        Invoice $invoice,
        int $createdBy,
        $faker,
        float $amount,
        PaymentStatus $status
    ): Payment {
        $paymentDate = Carbon::instance($faker->dateTimeBetween($invoice->issued_at, 'now'));

        return Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => round($amount, 2),
            'status' => $status->value,
            'payment_date' => $paymentDate,
            'created_by' => $createdBy,
            'payment_method' => $faker->randomElement(['cash', 'card', 'transfer']),
            'reference_number' => $faker->boolean(70) ? strtoupper($faker->bothify('REF-####??')) : null,
            'notes' => $faker->boolean(20) ? $faker->sentence() : null,
            'created_at' => $paymentDate,
            'updated_at' => $paymentDate,
        ]);
    }

    private function reservationWindow(ReservationStatus $status, $faker): array
    {
        $now = now();

        return match ($status) {
            ReservationStatus::Active => [
                $now->copy()->subDays($faker->numberBetween(1, 3)),
                $now->copy()->addDays($faker->numberBetween(3, 10)),
            ],
            ReservationStatus::Fulfilled => [
                $now->copy()->subDays($faker->numberBetween(20, 30)),
                $now->copy()->subDays($faker->numberBetween(10, 15)),
            ],
            ReservationStatus::Expired => [
                $now->copy()->subDays($faker->numberBetween(15, 25)),
                $now->copy()->subDays($faker->numberBetween(2, 7)),
            ],
            ReservationStatus::Cancelled => [
                $now->copy()->addDays($faker->numberBetween(2, 5)),
                $now->copy()->addDays($faker->numberBetween(7, 14)),
            ],
        };
    }

    private function vehicleForReservation(
        ReservationStatus $status,
        array $vehiclesByStatus,
        Collection $fallback
    ): Vehicle {
        $pool = match ($status) {
            ReservationStatus::Active => $vehiclesByStatus['reserved'],
            ReservationStatus::Fulfilled => $vehiclesByStatus['sold'],
            ReservationStatus::Expired => $vehiclesByStatus['available'],
            ReservationStatus::Cancelled => $vehiclesByStatus['available'],
        };

        if ($pool->isEmpty()) {
            return $fallback->random();
        }

        return $pool->random();
    }
}
