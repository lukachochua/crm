# CRM User Manual (Role-by-Role)

## Purpose
This manual explains the business flow of the CRM and provides detailed, role-by-role instructions for daily operations.

## Business Flow (Real-World Meaning)
- Customer: the person or organization you serve.
- Application: initial intake or request from a customer. Sales qualify it and move it through status.
- Order: the confirmed business transaction (commercial commitment).
- Reservation: assigns a specific vehicle/asset to an order for a time window.
- Invoice: billing document linked to an order.
- Payment: money movement linked to an invoice.
- Turnover overview: monthly summary derived from invoices and payments (read-only view).

## Relationships (How Records Connect)
- Customer has many Applications, Orders.
- Application belongs to a Customer and may convert into an Order.
- Order belongs to a Customer and may reference an Application.
- Reservation belongs to an Order and a Vehicle.
- Invoice belongs to an Order.
- Payment belongs to an Invoice.
- Turnover overview is derived from Invoices and Payments.

## Status Transitions (Allowed)
- Application: new -> reviewed -> approved -> converted; reviewed -> rejected.
- Order: draft -> confirmed -> completed OR draft -> confirmed -> cancelled.
- Reservation: active -> fulfilled OR expired OR cancelled.
- Invoice: draft -> issued -> partially_paid -> paid OR issued -> cancelled.
- Payment: pending -> completed -> reversed OR pending -> failed.

## Pricing and Amounts
Amounts are stored independently on Orders, Invoices, and Payments. The current system does not enforce automatic linkage across these totals (e.g., invoice total matching order total). This supports partial invoicing and partial payments.

## Common UI Behavior (All Roles)
- Login at /admin.
- Use list Search, Filters, and Sorting to find records.
- Status badges show the current state and color.
- Invalid status transitions are blocked with a validation message.
- Exports appear only if your role has full access for that module.

---
# Sales Role Manual

## Access
- Applications: full
- Orders: full
- Reservations: full
- Customers: full
- Vehicles: full
- Invoices: view-only

## Step-by-Step Flow
1) Create Customer
   - Go to Clients & Assets -> Customers -> Create.
   - Fill required fields: first_name, last_name, personal_id_or_tax_id, phone.
   - Save.

2) Create Vehicle
   - Go to Clients & Assets -> Vehicles -> Create.
   - Fill: vin_or_serial, type, status (available/reserved/sold).
   - Save.

3) Create Application (Lead/Request)
   - Go to Sales -> Applications -> Create.
   - Select Customer, set status = new, set requested_at.
   - Save.

4) Advance Application Status
   - Edit application.
   - Move along the allowed status path.

5) Create Order
   - Go to Sales -> Orders -> Create.
   - Select Customer, optionally link Application.
   - Fill order_number, status = draft, total_amount.
   - Save.

6) Advance Order Status
   - Edit order.
   - Move along the allowed status path.

7) Create Reservation
   - Go to Sales -> Reservations -> Create.
   - Select Order and Vehicle.
   - Set reserved_from, reserved_until, status = active.
   - Save.

8) Advance Reservation Status
   - Edit reservation.
   - Move along the allowed status path.

9) View Invoices (Read-only)
   - Go to Invoicing -> Invoices.
   - View invoice details only.

---
# Back Office Role Manual

## Access
- Applications: view-only
- Orders: full
- Reservations: full
- Customers: full
- Vehicles: full
- Invoices: full
- Payments: view-only

## Step-by-Step Flow
1) View Applications
   - Sales -> Applications (read-only).

2) Manage Customers and Vehicles
   - Same steps as Sales.

3) Create and Manage Orders
   - Same steps as Sales.

4) Create and Manage Reservations
   - Same steps as Sales.

5) Create Invoice
   - Invoicing -> Invoices -> Create.
   - Select Order, fill invoice_number, status = draft, total_amount, issued_at.
   - Save.

6) Advance Invoice Status
   - Edit invoice.
   - Move along the allowed status path.

7) View Payments (Read-only)
   - Finance -> Payments.

---
# Finance Role Manual

## Access
- Orders: view-only
- Customers: view-only
- Invoices: full
- Payments: full
- Turnover overview: view-only

## Step-by-Step Flow
1) View Orders and Customers
   - Sales -> Orders (read-only).
   - Clients & Assets -> Customers (read-only).

2) Create and Manage Invoices
   - Invoicing -> Invoices -> Create/Edit.
   - Move along the allowed status path.

3) Create and Manage Payments
   - Finance -> Payments -> Create.
   - Select Invoice, fill amount, status = pending, payment_date, payment_method.
   - Save.

4) Advance Payment Status
   - Edit payment.
   - Move along the allowed status path.

5) View Turnover Overview
   - Turnover -> Turnover Overview (read-only).

---
# Turnover Role Manual

## Access
- Turnover overview: full
- Invoices: view-only

## Step-by-Step Flow
1) View Turnover Overview
   - Turnover -> Turnover Overview.
   - Filter by period as needed.

2) Export Turnover Overview (if permitted)
   - Click Export, select columns, confirm.
   - Download link appears in notifications.

3) View Invoices (Read-only)
   - Invoicing -> Invoices.

---
# Admin Role Manual

## Access
- Full access to all modules.

## Step-by-Step Flow
- Perform any of the Sales, Back Office, Finance, or Turnover flows.

---
# Admin / Superadmin Appendix

## Assign Roles (Tinker)
1) php artisan tinker
2) Example:
   - $user = App\Models\User::where('email', 'user@example.com')->firstOrFail();
   - $user->assignRole('Sales');
   - $user->syncRoles(['Admin']);

## Seed Roles and Permissions
- php artisan db:seed --class=RolesAndPermissionsSeeder

## Create Filament User
- php artisan make:filament-user

## Reset Permission Cache
- php artisan permission:cache-reset
