<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\Hr\EmployeeStatus;
use App\Enums\Hr\FeedbackCycleStatus;
use App\Enums\Hr\FeedbackRequestStatus;
use App\Enums\Hr\KpiCycleStatus;
use App\Enums\Hr\KpiReportStatus;
use App\Enums\Hr\OnboardingStatus;
use App\Enums\Hr\OnboardingTaskStatus;
use App\Enums\Hr\PeriodType;
use App\Enums\Hr\QuestionType;
use App\Enums\Hr\RecruitmentStage;
use App\Enums\Hr\RaterType;
use App\Enums\Hr\SurveyStatus;
use App\Enums\Hr\TrainingAttendanceStatus;
use App\Enums\Hr\TrainingResultStatus;
use App\Enums\Hr\TrainingSessionStatus;
use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Models\Hr\Branch;
use App\Models\Hr\ContractType;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use App\Models\Hr\EmployeeDocument;
use App\Models\Hr\Feedback\FeedbackAnswer;
use App\Models\Hr\Feedback\FeedbackCycle;
use App\Models\Hr\Feedback\FeedbackQuestion;
use App\Models\Hr\Feedback\FeedbackRequest;
use App\Models\Hr\Kpi\KpiCycle;
use App\Models\Hr\Kpi\KpiReport;
use App\Models\Hr\Kpi\KpiReportItem;
use App\Models\Hr\Kpi\KpiTemplate;
use App\Models\Hr\Kpi\KpiTemplateItem;
use App\Models\Hr\Onboarding\EmployeeOnboarding;
use App\Models\Hr\Onboarding\EmployeeOnboardingTask;
use App\Models\Hr\Onboarding\OnboardingTemplate;
use App\Models\Hr\Onboarding\OnboardingTemplateTask;
use App\Models\Hr\Position;
use App\Models\Hr\Recruitment\Candidate;
use App\Models\Hr\Survey\EngagementSurvey;
use App\Models\Hr\Survey\SurveyAnswer;
use App\Models\Hr\Survey\SurveyQuestion;
use App\Models\Hr\Survey\SurveySubmission;
use App\Models\Hr\Training\TrainingParticipant;
use App\Models\Hr\Training\TrainingSession;
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

        $this->seedHrData($users);

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

        $users['hr_admin'] = User::factory()->create([
            'name' => 'HR Admin',
            'email' => 'hr@example.com',
            'password' => Hash::make('password'),
        ]);
        $users['hr_admin']->assignRole('hr_admin');

        $users['hr_manager'] = User::factory()->create([
            'name' => 'HR Manager',
            'email' => 'hr.manager@example.com',
            'password' => Hash::make('password'),
        ]);
        $users['hr_manager']->assignRole('hr_manager');

        $users['department_manager'] = User::factory()->create([
            'name' => 'Department Manager',
            'email' => 'dept.manager@example.com',
            'password' => Hash::make('password'),
        ]);
        $users['department_manager']->assignRole('department_manager');

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

        foreach ($orders->values() as $index => $order) {
            $status = $statuses[$index % count($statuses)];
            $issuedAt = Carbon::instance($faker->dateTimeBetween('-4 months', 'now'));
            $dueDate = $faker->boolean(85) ? $issuedAt->copy()->addDays(30) : null;
            $total = max(0.01, (float) $order->total_amount - (float) ($order->discount_amount ?? 0));

            $invoices->push(Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => $this->generateInvoiceNumber($issuedAt),
                'status' => $status->value,
                'total_amount' => $total,
                'issued_at' => $issuedAt,
                'due_date' => $dueDate,
                'notes' => $faker->boolean(20) ? $faker->sentence() : null,
                'created_at' => $issuedAt,
                'updated_at' => $issuedAt,
            ]));
        }

        $extraOrder = $orders->first();
        if ($extraOrder) {
            $issuedAt = Carbon::instance($faker->dateTimeBetween('-2 months', 'now'));
            $invoices->push(Invoice::create([
                'order_id' => $extraOrder->id,
                'invoice_number' => $this->generateInvoiceNumber($issuedAt),
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

    private function generateInvoiceNumber(Carbon $issuedAt): string
    {
        $prefix = $issuedAt->format('Ymd');

        do {
            $number = sprintf('INV-%s-%04d', $prefix, random_int(1, 9999));
        } while (Invoice::where('invoice_number', $number)->exists());

        return $number;
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

    private function seedHrData(array $users): void
    {
        $org = $this->seedHrOrg();
        $employees = $this->seedHrEmployees($users, $org);

        $this->seedEmployeeDocuments($employees, $users['hr_admin']);

        $kpiTemplates = $this->seedKpiTemplates($org['positions']);
        $kpiCycles = $this->seedKpiCycles();
        $this->seedKpiReports($employees, $kpiTemplates, $kpiCycles);

        $trainingSessions = $this->seedTrainingSessions($users);
        $this->seedTrainingParticipants($trainingSessions, $employees);

        $this->seedCandidates($org['positions'], $org['branches']);

        $onboardingTemplates = $this->seedOnboardingTemplates($org['departments'], $org['positions']);
        $this->seedEmployeeOnboardings($employees, $onboardingTemplates, $users);

        $this->seedFeedback($employees, $users);
        $this->seedEngagementSurveys($employees, $users['hr_admin']);
    }

    private function seedHrOrg(): array
    {
        $departments = [
            'hr' => Department::create([
                'name' => 'Human Resources',
                'code' => 'HR',
                'notes' => 'People operations and HR programs.',
            ]),
            'sales' => Department::create([
                'name' => 'Sales',
                'code' => 'SALES',
                'notes' => 'Commercial team focused on pipeline growth.',
            ]),
            'operations' => Department::create([
                'name' => 'Operations',
                'code' => 'OPS',
                'notes' => 'Core operations and service delivery.',
            ]),
        ];

        $positions = [
            'hr_admin' => Position::create([
                'name' => 'HR Administrator',
                'code' => 'HR-ADMIN',
                'notes' => 'Leads HR administration.',
            ]),
            'hr_manager' => Position::create([
                'name' => 'HR Manager',
                'code' => 'HR-MGR',
                'notes' => 'Manages HR programs and policies.',
            ]),
            'sales_rep' => Position::create([
                'name' => 'Sales Representative',
                'code' => 'SALES-REP',
                'notes' => 'Owns sales pipeline and customer engagement.',
            ]),
            'ops_analyst' => Position::create([
                'name' => 'Operations Analyst',
                'code' => 'OPS-ANL',
                'notes' => 'Supports operational reporting and improvement.',
            ]),
            'dept_manager' => Position::create([
                'name' => 'Department Manager',
                'code' => 'DEPT-MGR',
                'notes' => 'Leads department performance and people.',
            ]),
        ];

        $branches = [
            'hq' => Branch::create([
                'name' => 'Headquarters',
                'code' => 'HQ',
                'notes' => 'Primary office location.',
            ]),
            'north' => Branch::create([
                'name' => 'North Branch',
                'code' => 'NORTH',
                'notes' => 'Regional branch (north).',
            ]),
            'south' => Branch::create([
                'name' => 'South Branch',
                'code' => 'SOUTH',
                'notes' => 'Regional branch (south).',
            ]),
        ];

        $contractTypes = [
            'full_time' => ContractType::create([
                'name' => 'Full Time',
                'code' => 'FULLTIME',
                'description' => 'Standard full-time employment.',
                'is_active' => true,
            ]),
            'part_time' => ContractType::create([
                'name' => 'Part Time',
                'code' => 'PARTTIME',
                'description' => 'Reduced hours employment.',
                'is_active' => true,
            ]),
            'contractor' => ContractType::create([
                'name' => 'Contractor',
                'code' => 'CONTRACT',
                'description' => 'Fixed-term contract engagement.',
                'is_active' => true,
            ]),
        ];

        return compact('departments', 'positions', 'branches', 'contractTypes');
    }

    private function seedHrEmployees(array $users, array $org): array
    {
        $contractorUser = User::factory()->create([
            'name' => 'Taylor Contractor',
            'email' => 'contractor@example.com',
            'password' => Hash::make('password'),
        ]);

        $newHireUser = User::factory()->create([
            'name' => 'Alex Newhire',
            'email' => 'newhire@example.com',
            'password' => Hash::make('password'),
        ]);

        $today = now()->startOfDay();

        $employees = [];

        $employees['hr_admin'] = Employee::create([
            'user_id' => $users['hr_admin']->id,
            'department_id' => $org['departments']['hr']->id,
            'position_id' => $org['positions']['hr_admin']->id,
            'branch_id' => $org['branches']['hq']->id,
            'contract_type_id' => $org['contractTypes']['full_time']->id,
            'manager_user_id' => null,
            'start_date' => $today->copy()->subYears(3),
            'contract_end_date' => null,
            'status' => EmployeeStatus::Active->value,
            'notes' => 'Primary HR contact.',
        ]);

        $employees['hr_manager'] = Employee::create([
            'user_id' => $users['hr_manager']->id,
            'department_id' => $org['departments']['hr']->id,
            'position_id' => $org['positions']['hr_manager']->id,
            'branch_id' => $org['branches']['hq']->id,
            'contract_type_id' => $org['contractTypes']['full_time']->id,
            'manager_user_id' => $users['hr_admin']->id,
            'start_date' => $today->copy()->subYears(2),
            'contract_end_date' => null,
            'status' => EmployeeStatus::Active->value,
            'notes' => 'Oversees HR programs.',
        ]);

        $employees['department_manager'] = Employee::create([
            'user_id' => $users['department_manager']->id,
            'department_id' => $org['departments']['sales']->id,
            'position_id' => $org['positions']['dept_manager']->id,
            'branch_id' => $org['branches']['north']->id,
            'contract_type_id' => $org['contractTypes']['full_time']->id,
            'manager_user_id' => $users['hr_admin']->id,
            'start_date' => $today->copy()->subYears(1),
            'contract_end_date' => null,
            'status' => EmployeeStatus::Active->value,
            'notes' => 'Leads the sales team.',
        ]);

        $employees['sales'] = Employee::create([
            'user_id' => $users['sales']->id,
            'department_id' => $org['departments']['sales']->id,
            'position_id' => $org['positions']['sales_rep']->id,
            'branch_id' => $org['branches']['north']->id,
            'contract_type_id' => $org['contractTypes']['full_time']->id,
            'manager_user_id' => $users['department_manager']->id,
            'start_date' => $today->copy()->subMonths(10),
            'contract_end_date' => null,
            'status' => EmployeeStatus::Active->value,
            'notes' => 'Top performer in pipeline conversion.',
            'feedback_summary' => [
                'strengths' => 'Consistent outreach and client follow-up.',
                'focus' => 'Improve deal documentation.',
            ],
            'feedback_last_calculated_at' => $today->copy()->subDays(5),
        ]);

        $employees['turnover'] = Employee::create([
            'user_id' => $users['turnover']->id,
            'department_id' => $org['departments']['sales']->id,
            'position_id' => $org['positions']['sales_rep']->id,
            'branch_id' => $org['branches']['south']->id,
            'contract_type_id' => $org['contractTypes']['part_time']->id,
            'manager_user_id' => $users['department_manager']->id,
            'start_date' => $today->copy()->subMonths(14),
            'contract_end_date' => null,
            'status' => EmployeeStatus::Suspended->value,
            'notes' => 'On temporary suspension pending review.',
        ]);

        $employees['backoffice'] = Employee::create([
            'user_id' => $users['backoffice']->id,
            'department_id' => $org['departments']['operations']->id,
            'position_id' => $org['positions']['ops_analyst']->id,
            'branch_id' => $org['branches']['hq']->id,
            'contract_type_id' => $org['contractTypes']['full_time']->id,
            'manager_user_id' => $users['admin']->id,
            'start_date' => $today->copy()->subMonths(18),
            'contract_end_date' => null,
            'status' => EmployeeStatus::Active->value,
        ]);

        $employees['finance'] = Employee::create([
            'user_id' => $users['finance']->id,
            'department_id' => $org['departments']['operations']->id,
            'position_id' => $org['positions']['ops_analyst']->id,
            'branch_id' => $org['branches']['hq']->id,
            'contract_type_id' => $org['contractTypes']['full_time']->id,
            'manager_user_id' => $users['admin']->id,
            'start_date' => $today->copy()->subMonths(20),
            'contract_end_date' => null,
            'status' => EmployeeStatus::Active->value,
        ]);

        $employees['admin'] = Employee::create([
            'user_id' => $users['admin']->id,
            'department_id' => $org['departments']['operations']->id,
            'position_id' => $org['positions']['dept_manager']->id,
            'branch_id' => $org['branches']['hq']->id,
            'contract_type_id' => $org['contractTypes']['full_time']->id,
            'manager_user_id' => null,
            'start_date' => $today->copy()->subYears(4),
            'contract_end_date' => null,
            'status' => EmployeeStatus::Active->value,
            'notes' => 'Executive sponsor for operations.',
        ]);

        $employees['contractor'] = Employee::create([
            'user_id' => $contractorUser->id,
            'department_id' => $org['departments']['sales']->id,
            'position_id' => $org['positions']['sales_rep']->id,
            'branch_id' => $org['branches']['south']->id,
            'contract_type_id' => $org['contractTypes']['contractor']->id,
            'manager_user_id' => $users['department_manager']->id,
            'start_date' => $today->copy()->subMonths(2),
            'contract_end_date' => $today->copy()->addDays(18),
            'status' => EmployeeStatus::Active->value,
            'notes' => 'Short-term contractor assignment.',
        ]);

        $employees['new_hire'] = Employee::create([
            'user_id' => $newHireUser->id,
            'department_id' => $org['departments']['sales']->id,
            'position_id' => $org['positions']['sales_rep']->id,
            'branch_id' => $org['branches']['north']->id,
            'contract_type_id' => $org['contractTypes']['part_time']->id,
            'manager_user_id' => $users['department_manager']->id,
            'start_date' => $today->copy()->subDays(12),
            'contract_end_date' => null,
            'status' => EmployeeStatus::Active->value,
            'notes' => 'Recently onboarded and in ramp-up phase.',
        ]);

        return $employees;
    }

    private function seedEmployeeDocuments(array $employees, User $uploader): void
    {
        EmployeeDocument::create([
            'employee_id' => $employees['hr_admin']->id,
            'document_type' => 'contract',
            'title' => 'HR Admin Contract',
            'file_path' => 'hr/employee-documents/hr-admin/contract.pdf',
            'file_name' => 'contract.pdf',
            'mime_type' => 'application/pdf',
            'expires_on' => null,
            'uploaded_by' => $uploader->id,
            'notes' => 'Signed employment agreement.',
        ]);

        EmployeeDocument::create([
            'employee_id' => $employees['sales']->id,
            'document_type' => 'id',
            'title' => 'Sales ID Card',
            'file_path' => 'hr/employee-documents/sales/id-card.pdf',
            'file_name' => 'id-card.pdf',
            'mime_type' => 'application/pdf',
            'expires_on' => now()->addYear(),
            'uploaded_by' => $uploader->id,
            'notes' => 'Company-issued identification.',
        ]);

        EmployeeDocument::create([
            'employee_id' => $employees['backoffice']->id,
            'document_type' => 'certificate',
            'title' => 'Operations Compliance Certificate',
            'file_path' => 'hr/employee-documents/backoffice/compliance.pdf',
            'file_name' => 'compliance.pdf',
            'mime_type' => 'application/pdf',
            'expires_on' => now()->addMonths(6),
            'uploaded_by' => $uploader->id,
            'notes' => 'Annual compliance training certificate.',
        ]);
    }

    private function seedKpiTemplates(array $positions): array
    {
        $salesTemplate = KpiTemplate::create([
            'name' => 'Sales Performance',
            'position_id' => $positions['sales_rep']->id,
            'description' => 'Monthly sales KPIs for revenue and activity.',
            'is_active' => true,
        ]);

        $salesItems = [
            KpiTemplateItem::create([
                'kpi_template_id' => $salesTemplate->id,
                'title' => 'Monthly Revenue',
                'description' => 'Target revenue for the period.',
                'weight' => 50,
                'sort_order' => 1,
            ]),
            KpiTemplateItem::create([
                'kpi_template_id' => $salesTemplate->id,
                'title' => 'Qualified Leads',
                'description' => 'Number of qualified leads generated.',
                'weight' => 30,
                'sort_order' => 2,
            ]),
            KpiTemplateItem::create([
                'kpi_template_id' => $salesTemplate->id,
                'title' => 'Client Follow-ups',
                'description' => 'Follow-up cadence and responsiveness.',
                'weight' => 20,
                'sort_order' => 3,
            ]),
        ];

        $opsTemplate = KpiTemplate::create([
            'name' => 'Operations Performance',
            'position_id' => $positions['ops_analyst']->id,
            'description' => 'Operational accuracy and turnaround KPIs.',
            'is_active' => true,
        ]);

        $opsItems = [
            KpiTemplateItem::create([
                'kpi_template_id' => $opsTemplate->id,
                'title' => 'Process Accuracy',
                'description' => 'Error-free completion rate.',
                'weight' => 50,
                'sort_order' => 1,
            ]),
            KpiTemplateItem::create([
                'kpi_template_id' => $opsTemplate->id,
                'title' => 'Cycle Time',
                'description' => 'Average completion time for tasks.',
                'weight' => 30,
                'sort_order' => 2,
            ]),
            KpiTemplateItem::create([
                'kpi_template_id' => $opsTemplate->id,
                'title' => 'Policy Compliance',
                'description' => 'Adherence to process standards.',
                'weight' => 20,
                'sort_order' => 3,
            ]),
        ];

        return [
            'templates' => [
                'sales' => $salesTemplate,
                'operations' => $opsTemplate,
            ],
            'items' => [
                'sales' => $salesItems,
                'operations' => $opsItems,
            ],
        ];
    }

    private function seedKpiCycles(): array
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $quarterStart = now()->subMonths(3)->startOfQuarter();
        $quarterEnd = $quarterStart->copy()->endOfQuarter();

        $monthly = KpiCycle::create([
            'period_type' => PeriodType::Month->value,
            'period_start' => $monthStart,
            'period_end' => $monthEnd,
            'label' => $monthStart->format('F Y'),
            'status' => KpiCycleStatus::Open->value,
        ]);

        $quarterly = KpiCycle::create([
            'period_type' => PeriodType::Quarter->value,
            'period_start' => $quarterStart,
            'period_end' => $quarterEnd,
            'label' => sprintf('Q%s %s', $quarterStart->quarter, $quarterStart->year),
            'status' => KpiCycleStatus::Closed->value,
        ]);

        return [
            'monthly' => $monthly,
            'quarterly' => $quarterly,
        ];
    }

    private function seedKpiReports(array $employees, array $kpiTemplates, array $kpiCycles): void
    {
        $salesReport = KpiReport::create([
            'employee_id' => $employees['sales']->id,
            'kpi_template_id' => $kpiTemplates['templates']['sales']->id,
            'kpi_cycle_id' => $kpiCycles['monthly']->id,
            'status' => KpiReportStatus::Submitted->value,
            'self_submitted_at' => now()->subDays(5),
            'manager_reviewed_at' => null,
        ]);

        $this->createKpiReportItems(
            $salesReport,
            $kpiTemplates['items']['sales'],
            [4.2, 4.0, 3.8],
            []
        );

        $draftReport = KpiReport::create([
            'employee_id' => $employees['turnover']->id,
            'kpi_template_id' => $kpiTemplates['templates']['sales']->id,
            'kpi_cycle_id' => $kpiCycles['monthly']->id,
            'status' => KpiReportStatus::Draft->value,
            'self_submitted_at' => null,
            'manager_reviewed_at' => null,
        ]);

        $this->createKpiReportItems(
            $draftReport,
            $kpiTemplates['items']['sales'],
            [],
            []
        );

        $opsReport = KpiReport::create([
            'employee_id' => $employees['backoffice']->id,
            'kpi_template_id' => $kpiTemplates['templates']['operations']->id,
            'kpi_cycle_id' => $kpiCycles['quarterly']->id,
            'status' => KpiReportStatus::ManagerReviewed->value,
            'self_submitted_at' => now()->subDays(12),
            'manager_reviewed_at' => now()->subDays(4),
        ]);

        $this->createKpiReportItems(
            $opsReport,
            $kpiTemplates['items']['operations'],
            [4.0, 3.6, 4.4],
            [4.1, 3.7, 4.2]
        );
    }

    private function createKpiReportItems(
        KpiReport $report,
        array $templateItems,
        array $selfScores,
        array $managerScores
    ): void {
        foreach ($templateItems as $index => $item) {
            KpiReportItem::create([
                'kpi_report_id' => $report->id,
                'kpi_template_item_id' => $item->id,
                'self_score' => $selfScores[$index] ?? null,
                'manager_score' => $managerScores[$index] ?? null,
                'self_comment' => $selfScores ? 'Self assessment noted.' : null,
                'manager_comment' => $managerScores ? 'Manager review complete.' : null,
            ]);
        }
    }

    private function seedTrainingSessions(array $users): array
    {
        $upcomingStart = now()->addDays(7)->setTime(9, 0);
        $upcomingEnd = $upcomingStart->copy()->addHours(6);

        $completedStart = now()->subDays(25)->setTime(10, 0);
        $completedEnd = $completedStart->copy()->addHours(4);

        $salesBootcamp = TrainingSession::create([
            'title' => 'Sales Bootcamp',
            'description' => 'Intensive training for prospecting and pipeline health.',
            'starts_at' => $upcomingStart,
            'ends_at' => $upcomingEnd,
            'location' => 'HQ - Training Room',
            'trainer_user_id' => $users['hr_manager']->id,
            'status' => TrainingSessionStatus::Scheduled->value,
        ]);

        $opsRefresher = TrainingSession::create([
            'title' => 'Operations Refresher',
            'description' => 'Quarterly operations compliance refresher.',
            'starts_at' => $completedStart,
            'ends_at' => $completedEnd,
            'location' => 'HQ - Ops Floor',
            'trainer_user_id' => $users['hr_admin']->id,
            'status' => TrainingSessionStatus::Completed->value,
        ]);

        return [
            'sales' => $salesBootcamp,
            'ops' => $opsRefresher,
        ];
    }

    private function seedTrainingParticipants(array $sessions, array $employees): void
    {
        TrainingParticipant::create([
            'training_session_id' => $sessions['sales']->id,
            'employee_id' => $employees['sales']->id,
            'attendance_status' => TrainingAttendanceStatus::Confirmed->value,
            'result_status' => null,
            'result_score' => null,
            'completed_at' => null,
            'notes' => 'Confirmed attendance.',
        ]);

        TrainingParticipant::create([
            'training_session_id' => $sessions['sales']->id,
            'employee_id' => $employees['new_hire']->id,
            'attendance_status' => TrainingAttendanceStatus::Invited->value,
            'result_status' => null,
            'result_score' => null,
            'completed_at' => null,
            'notes' => 'Awaiting confirmation.',
        ]);

        TrainingParticipant::create([
            'training_session_id' => $sessions['ops']->id,
            'employee_id' => $employees['backoffice']->id,
            'attendance_status' => TrainingAttendanceStatus::Attended->value,
            'result_status' => TrainingResultStatus::Passed->value,
            'result_score' => 88.5,
            'completed_at' => $sessions['ops']->ends_at,
            'notes' => 'Completed with strong results.',
        ]);

        TrainingParticipant::create([
            'training_session_id' => $sessions['ops']->id,
            'employee_id' => $employees['finance']->id,
            'attendance_status' => TrainingAttendanceStatus::Attended->value,
            'result_status' => TrainingResultStatus::Failed->value,
            'result_score' => 61.0,
            'completed_at' => $sessions['ops']->ends_at,
            'notes' => 'Needs follow-up session.',
        ]);
    }

    private function seedCandidates(array $positions, array $branches): void
    {
        $faker = fake();

        Candidate::create([
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'email' => $faker->unique()->safeEmail(),
            'phone' => $faker->numerify('555-2##-####'),
            'position_id' => $positions['sales_rep']->id,
            'branch_id' => $branches['north']->id,
            'stage' => RecruitmentStage::Application->value,
            'applied_at' => now()->subDays(12),
            'source' => 'LinkedIn',
            'notes' => 'Early screening stage.',
        ]);

        Candidate::create([
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'email' => $faker->unique()->safeEmail(),
            'phone' => $faker->numerify('555-3##-####'),
            'position_id' => $positions['sales_rep']->id,
            'branch_id' => $branches['south']->id,
            'stage' => RecruitmentStage::Interview->value,
            'applied_at' => now()->subDays(20),
            'source' => 'Referral',
            'notes' => 'Interview scheduled with hiring manager.',
        ]);

        Candidate::create([
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'email' => $faker->unique()->safeEmail(),
            'phone' => $faker->numerify('555-4##-####'),
            'position_id' => $positions['ops_analyst']->id,
            'branch_id' => $branches['hq']->id,
            'stage' => RecruitmentStage::Offer->value,
            'applied_at' => now()->subDays(28),
            'source' => 'Careers Page',
            'notes' => 'Offer extended and awaiting response.',
        ]);
    }

    private function seedOnboardingTemplates(array $departments, array $positions): array
    {
        $salesTemplate = OnboardingTemplate::create([
            'name' => 'Sales Onboarding',
            'description' => 'Ramp-up plan for new sales reps.',
            'department_id' => $departments['sales']->id,
            'position_id' => $positions['sales_rep']->id,
            'is_active' => true,
        ]);

        $salesTasks = [
            OnboardingTemplateTask::create([
                'onboarding_template_id' => $salesTemplate->id,
                'title' => 'Issue CRM credentials',
                'description' => 'Create access to the CRM and sales tools.',
                'sort_order' => 1,
                'default_due_days' => 2,
            ]),
            OnboardingTemplateTask::create([
                'onboarding_template_id' => $salesTemplate->id,
                'title' => 'Product walkthrough',
                'description' => 'Review key product offerings and pricing.',
                'sort_order' => 2,
                'default_due_days' => 5,
            ]),
            OnboardingTemplateTask::create([
                'onboarding_template_id' => $salesTemplate->id,
                'title' => 'Shadow outbound calls',
                'description' => 'Observe at least two live sales calls.',
                'sort_order' => 3,
                'default_due_days' => 7,
            ]),
        ];

        $opsTemplate = OnboardingTemplate::create([
            'name' => 'Operations Onboarding',
            'description' => 'Core process training for operations staff.',
            'department_id' => $departments['operations']->id,
            'position_id' => $positions['ops_analyst']->id,
            'is_active' => true,
        ]);

        $opsTasks = [
            OnboardingTemplateTask::create([
                'onboarding_template_id' => $opsTemplate->id,
                'title' => 'System access setup',
                'description' => 'Provision access to internal operations systems.',
                'sort_order' => 1,
                'default_due_days' => 2,
            ]),
            OnboardingTemplateTask::create([
                'onboarding_template_id' => $opsTemplate->id,
                'title' => 'Process documentation review',
                'description' => 'Review SOPs and compliance checklists.',
                'sort_order' => 2,
                'default_due_days' => 6,
            ]),
            OnboardingTemplateTask::create([
                'onboarding_template_id' => $opsTemplate->id,
                'title' => 'Shadow operations workflow',
                'description' => 'Shadow a senior analyst during live work.',
                'sort_order' => 3,
                'default_due_days' => 9,
            ]),
        ];

        return [
            'sales' => [
                'template' => $salesTemplate,
                'tasks' => $salesTasks,
            ],
            'ops' => [
                'template' => $opsTemplate,
                'tasks' => $opsTasks,
            ],
        ];
    }

    private function seedEmployeeOnboardings(array $employees, array $templates, array $users): void
    {
        $salesStart = now()->subDays(15);
        $salesOnboarding = EmployeeOnboarding::create([
            'employee_id' => $employees['new_hire']->id,
            'onboarding_template_id' => $templates['sales']['template']->id,
            'status' => OnboardingStatus::InProgress->value,
            'start_date' => $salesStart,
            'due_date' => $salesStart->copy()->addDays(10),
            'completed_at' => null,
            'notes' => 'Onboarding in progress with delayed tasks.',
        ]);

        $contractStart = now()->subDays(5);
        $contractOnboarding = EmployeeOnboarding::create([
            'employee_id' => $employees['contractor']->id,
            'onboarding_template_id' => $templates['sales']['template']->id,
            'status' => OnboardingStatus::NotStarted->value,
            'start_date' => $contractStart,
            'due_date' => $contractStart->copy()->addDays(12),
            'completed_at' => null,
            'notes' => 'Scheduled onboarding for contractor.',
        ]);

        $opsStart = now()->subDays(40);
        $opsOnboarding = EmployeeOnboarding::create([
            'employee_id' => $employees['backoffice']->id,
            'onboarding_template_id' => $templates['ops']['template']->id,
            'status' => OnboardingStatus::Completed->value,
            'start_date' => $opsStart,
            'due_date' => $opsStart->copy()->addDays(12),
            'completed_at' => $opsStart->copy()->addDays(14),
            'notes' => 'Onboarding completed successfully.',
        ]);

        $this->createEmployeeOnboardingTasks(
            $salesOnboarding,
            $templates['sales']['tasks'],
            [
                OnboardingTaskStatus::Completed->value,
                OnboardingTaskStatus::InProgress->value,
                OnboardingTaskStatus::Pending->value,
            ],
            $users['department_manager']->id
        );

        $this->createEmployeeOnboardingTasks(
            $contractOnboarding,
            $templates['sales']['tasks'],
            [
                OnboardingTaskStatus::Pending->value,
                OnboardingTaskStatus::Pending->value,
                OnboardingTaskStatus::Pending->value,
            ],
            $users['department_manager']->id
        );

        $this->createEmployeeOnboardingTasks(
            $opsOnboarding,
            $templates['ops']['tasks'],
            [
                OnboardingTaskStatus::Completed->value,
                OnboardingTaskStatus::Completed->value,
                OnboardingTaskStatus::Completed->value,
            ],
            $users['hr_manager']->id
        );
    }

    private function createEmployeeOnboardingTasks(
        EmployeeOnboarding $onboarding,
        array $templateTasks,
        array $statuses,
        ?int $assigneeId
    ): void {
        foreach ($templateTasks as $index => $task) {
            $status = $statuses[$index] ?? OnboardingTaskStatus::Pending->value;
            $dueDate = Carbon::parse($onboarding->start_date)
                ->addDays($task->default_due_days ?? 5)
                ->startOfDay();

            EmployeeOnboardingTask::create([
                'employee_onboarding_id' => $onboarding->id,
                'onboarding_template_task_id' => $task->id,
                'assigned_to_user_id' => $assigneeId,
                'status' => $status,
                'due_date' => $dueDate,
                'completed_at' => $status === OnboardingTaskStatus::Completed->value
                    ? Carbon::parse($onboarding->completed_at ?? now())
                    : null,
                'notes' => $status === OnboardingTaskStatus::Pending->value
                    ? 'Awaiting completion.'
                    : null,
            ]);
        }
    }

    private function seedFeedback(array $employees, array $users): void
    {
        $cycleStart = now()->subMonth()->startOfMonth();
        $cycleEnd = now()->addMonth()->endOfMonth();

        $cycle = FeedbackCycle::create([
            'name' => 'FY Review Cycle',
            'period_start' => $cycleStart,
            'period_end' => $cycleEnd,
            'status' => FeedbackCycleStatus::Open->value,
        ]);

        $questions = [
            FeedbackQuestion::create([
                'feedback_cycle_id' => $cycle->id,
                'question_text' => 'Delivers on team goals consistently.',
                'weight' => 40,
                'sort_order' => 1,
            ]),
            FeedbackQuestion::create([
                'feedback_cycle_id' => $cycle->id,
                'question_text' => 'Collaborates effectively with peers.',
                'weight' => 35,
                'sort_order' => 2,
            ]),
            FeedbackQuestion::create([
                'feedback_cycle_id' => $cycle->id,
                'question_text' => 'Communicates proactively and clearly.',
                'weight' => 25,
                'sort_order' => 3,
            ]),
        ];

        $managerRequest = FeedbackRequest::create([
            'feedback_cycle_id' => $cycle->id,
            'employee_id' => $employees['sales']->id,
            'rater_user_id' => $users['department_manager']->id,
            'rater_type' => RaterType::Manager->value,
            'status' => FeedbackRequestStatus::Submitted->value,
            'requested_at' => now()->subDays(7),
            'submitted_at' => now()->subDays(2),
        ]);

        foreach ($questions as $question) {
            FeedbackAnswer::create([
                'feedback_request_id' => $managerRequest->id,
                'feedback_question_id' => $question->id,
                'score' => 4.2,
                'comment' => 'Strong performance with room to grow.',
            ]);
        }

        FeedbackRequest::create([
            'feedback_cycle_id' => $cycle->id,
            'employee_id' => $employees['sales']->id,
            'rater_user_id' => $employees['sales']->user_id,
            'rater_type' => RaterType::Self->value,
            'status' => FeedbackRequestStatus::Pending->value,
            'requested_at' => now()->subDays(3),
            'submitted_at' => null,
        ]);

        $peerRequest = FeedbackRequest::create([
            'feedback_cycle_id' => $cycle->id,
            'employee_id' => $employees['backoffice']->id,
            'rater_user_id' => $users['hr_manager']->id,
            'rater_type' => RaterType::Peer->value,
            'status' => FeedbackRequestStatus::Submitted->value,
            'requested_at' => now()->subDays(9),
            'submitted_at' => now()->subDays(4),
        ]);

        foreach ($questions as $question) {
            FeedbackAnswer::create([
                'feedback_request_id' => $peerRequest->id,
                'feedback_question_id' => $question->id,
                'score' => 3.8,
                'comment' => 'Reliable contributor to the team.',
            ]);
        }
    }

    private function seedEngagementSurveys(array $employees, User $creator): void
    {
        $survey = EngagementSurvey::create([
            'title' => 'Employee Engagement Pulse',
            'description' => 'Quarterly survey on engagement and culture.',
            'status' => SurveyStatus::Open->value,
            'opens_at' => now()->subDays(3),
            'closes_at' => now()->addDays(10),
            'created_by' => $creator->id,
        ]);

        $questions = [
            SurveyQuestion::create([
                'engagement_survey_id' => $survey->id,
                'question_text' => 'How satisfied are you with your current role?',
                'question_type' => QuestionType::Scale->value,
                'config' => json_encode(['min' => 1, 'max' => 5]),
                'sort_order' => 1,
            ]),
            SurveyQuestion::create([
                'engagement_survey_id' => $survey->id,
                'question_text' => 'Which benefits matter most to you?',
                'question_type' => QuestionType::MultiChoice->value,
                'config' => json_encode(['options' => ['Health', 'Remote Work', 'Training']]),
                'sort_order' => 2,
            ]),
            SurveyQuestion::create([
                'engagement_survey_id' => $survey->id,
                'question_text' => 'Any comments for leadership?',
                'question_type' => QuestionType::Text->value,
                'config' => null,
                'sort_order' => 3,
            ]),
        ];

        $submissionSales = SurveySubmission::create([
            'engagement_survey_id' => $survey->id,
            'user_id' => $employees['sales']->user_id,
            'submitted_at' => now()->subDays(1),
        ]);

        $this->createSurveyAnswers($submissionSales, $questions, [
            '4',
            json_encode(['Health', 'Remote Work']),
            'Appreciate the team support and training resources.',
        ]);

        $submissionOps = SurveySubmission::create([
            'engagement_survey_id' => $survey->id,
            'user_id' => $employees['backoffice']->user_id,
            'submitted_at' => now()->subDays(2),
        ]);

        $this->createSurveyAnswers($submissionOps, $questions, [
            '3',
            json_encode(['Training']),
            'Would like clearer process documentation.',
        ]);
    }

    private function createSurveyAnswers(
        SurveySubmission $submission,
        array $questions,
        array $answers
    ): void {
        foreach ($questions as $index => $question) {
            SurveyAnswer::create([
                'survey_submission_id' => $submission->id,
                'survey_question_id' => $question->id,
                'answer_value' => $answers[$index] ?? '',
            ]);
        }
    }
}
