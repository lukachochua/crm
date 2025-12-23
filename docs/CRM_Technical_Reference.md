# CRM Technical Reference

## Purpose
This document is the technical source of truth for CRM architecture, data model, permissions, runtime flow, and invoicing behavior.

## Architecture Overview
- Laravel 12 + Filament v3 internal CRM.
- Spatie Laravel Permission + Laravel Policies for access control.
- Normalized relational schema with soft deletes for business entities.
- Enum-based status fields with strict transition validation.
- Audit logs for status changes, financial actions, and deletions.
- Turnover overview derived from invoices and payments (read-only view).
- Audit logs are intended to be immutable and never deleted.

## Admin Panel Routing
- `/admin` is a landing page that redirects by role to `/admin/crm` or `/admin/hr`.
- CRM dashboards live under `app/Filament/Pages/Crm`.

## Role and Permission Matrix
Legend:
- view = read-only
- full = view + create + update + delete + export
- none = no access

| Capability | Sales | Back Office | Finance | Turnover | Admin |
| --- | --- | --- | --- | --- | --- |
| Applications / Requests | full | view | none | none | full |
| Orders | full | full | view | none | full |
| Reservations | full | full | none | none | full |
| Customers database | full | full | view | none | full |
| Vehicles / Assets | full | full | none | none | full |
| Invoices | view | full | full | view | full |
| Payments | none | view | full | none | full |
| Turnover overview | none | none | view | full | full |

## Data Model (Schema Overview)
### applications
- id, customer_id, status, requested_at, created_by, description, source, internal_notes, timestamps, deleted_at

### orders
- id, customer_id, application_id, order_number, status, total_amount, discount_amount, notes, created_by, timestamps, deleted_at

### reservations
- id, order_id, vehicle_id, status, reserved_from, reserved_until, notes, timestamps, deleted_at

### customers
- id, first_name, last_name, personal_id_or_tax_id, phone, email, address, notes, timestamps, deleted_at

### vehicles
- id, vin_or_serial, type, status, model, year, color, notes, timestamps, deleted_at

### invoices
- id, order_id, invoice_number, status, total_amount, issued_at, due_date, notes, timestamps, deleted_at

### payments
- id, invoice_id, amount, status, payment_date, created_by, payment_method, reference_number, notes, timestamps, deleted_at

### audit_logs
- id, auditable_type, auditable_id, action_type, performed_by, performed_at, before_state, after_state, amount_before, amount_after, currency, notes, ip_address

### turnover_overviews (view)
- period (YYYY-MM), total_invoiced, total_paid, outstanding_amount
This view is read-only and not writable.

## Relationships (How Records Connect)
- Customer has many Applications and Orders.
- Application belongs to a Customer and may convert into an Order.
- Order belongs to a Customer and may reference an Application.
- Reservation belongs to an Order and a Vehicle.
- Invoice belongs to an Order.
- Payment belongs to an Invoice.
- TurnoverOverview is a database view from invoices and payments.

## Status Transitions
- application_status: new -> reviewed -> approved -> converted; reviewed -> rejected
- order_status: draft -> confirmed -> completed | cancelled
- reservation_status: active -> fulfilled | expired | cancelled
- invoice_status: draft -> issued -> partially_paid | paid | cancelled; partially_paid -> paid
- payment_status: pending -> completed | failed; completed -> reversed

## Amounts and Totals
Amounts are stored independently on Orders, Invoices, and Payments. The system does not enforce automatic linkage across totals (e.g., invoice total matching order total), enabling partial invoicing and partial payments.

## Runtime Flow
### High-Level Runtime Flow (Text Diagram)
```
Browser (Filament UI)
    |
    v
Filament Page/Action
    |
    v
Policy Check (Gate::allows / Model Policy)
    |
    +--> Deny -> 403 / Action hidden
    |
    v
Eloquent Model (create/update/delete)
    |
    v
Status Guard (EnforcesStatusTransitions)
    |
    +--> Invalid transition -> Validation error
    |
    v
DB Write (MySQL/Postgres)
    |
    v
Observers (status/payment/deletion)
    |
    v
Audit Logger -> audit_logs table
```

### Access Control Flow (User -> Role -> Permission)
```
User (App\Models\User, HasRoles)
    |
    v
Role(s) and Permissions (Spatie tables)
    |
    v
Policy methods call $user->can('entity.action')
    |
    v
Filament checks canViewAny / canCreate / canEdit / canDelete
```

### Export Flow (When Export is Clicked)
```
ExportAction (Filament)
    |
    v
Exporter class (app/Filament/Exports/Crm/*Exporter.php)
    |
    v
exports table row created
    |
    v
Queue job runs (or sync)
    |
    v
File stored on disk + notification created
    |
    v
User downloads via /admin/exports/{id}/download
```
In development, a queue worker should be running for exports to finish.

## Invoicing Process (Current Behavior)
### Concepts and Relationships
- Order: the commercial commitment. It can have many invoices.
- Invoice: a billing record linked to one order.
- Payment: a money movement linked to one invoice.
- Turnover overview: a monthly aggregation of invoice totals vs. payments.

### Invoice Lifecycle and Status
- draft -> issued -> partially_paid -> paid
- issued -> cancelled

Status transitions are enforced by model rules, so invalid jumps are blocked.

### Manual Invoicing (UI Flow)
Invoices can always be created and managed manually by Back Office or Finance.
1) Go to Invoicing -> Invoices -> Create.
2) Select the Order, then fill invoice_number, status, total_amount, issued_at.
3) Save and update status as payment progresses.

Multiple invoices per order are allowed (partial billing is supported).

### Auto-Invoice on Order Completion
When an order status changes to `completed`, the system attempts to auto-create
an invoice with defaults:
- status: draft
- issued_at: now()
- due_date: null
- notes: "Auto-created when order completed."
- invoice_number: "INV-YYYYMMDD-####" (randomized per day, uniqueness checked)

#### How the Amount Is Calculated
Billable total is defined as:
`billable_total = max(0, order.total_amount - order.discount_amount)`

If there are existing invoices, the auto-invoice uses the remaining amount:
`remaining = billable_total - sum(existing invoices)`

If remaining <= 0, auto-invoice is skipped.

#### When Auto-Invoice Is Skipped
Auto-invoice will not create a new invoice if:
- there is no remaining amount to invoice, OR
- the order already has invoices and it is fully paid.

"Fully paid" is computed as:
`sum(completed payments) - sum(reversed payments) >= billable_total`

When auto-invoice is skipped in the web UI, a warning notification is shown.
In console contexts, notifications are suppressed.

### Payments and Settlement
Payments are linked to invoices and have statuses:
- pending -> completed -> reversed
- pending -> failed

Only completed and reversed payments affect the "fully paid" calculation.
Pending or failed payments do not count toward settlement.

### Turnover Overview
Turnover is computed by a database view that sums:
- total invoiced (from invoices)
- total paid (from payments)
It also shows the outstanding amount for each month.

### Audit Logging
Audit logs are recorded when:
- invoice status changes
- invoice total changes
- payment is created or changes status
- order/application/reservation status changes
- records are deleted

Some audit events require an authenticated user. The auto-invoice path does not
create audit entries on its own, but later invoice changes will be logged.
Audit logs are not editable in the UI.
Running actions in console without an authenticated user can throw exceptions.

### Current Assumptions and Defaults
- Order `total_amount` is the pre-discount price.
- Order `discount_amount` is an absolute amount (not a percentage).
- Discounts apply once at the order level, not per invoice.
- Multiple invoices per order are allowed; auto-invoice uses remaining balance.

### Known Limitations / Future Decisions
- Whether auto-invoicing should trigger on `confirmed` vs `completed`.
- Whether draft invoices should allow `issued_at = null`.
- Whether due dates should be set automatically (e.g., +30 days).
- The definitive invoice number format/sequence for production.
- Whether auto-invoice should block order completion or only warn/skip.

## Filament UI Architecture
- Resources define forms (create/edit) and tables (list) per entity.
- Relation managers expose nested data where relevant.
- Dashboards show KPIs, charts, and recent activity widgets.
- Visibility of navigation and actions is controlled by policies and `canViewAny` checks.

## Setup Instructions
1) Configure `.env` for MySQL (defaults already set to `crm`).
2) Install dependencies: `composer install`.
3) Run migrations: `php artisan migrate`.
4) Seed roles and permissions: `php artisan db:seed`.
5) Create a Filament admin user: `php artisan make:filament-user`.
6) Access Filament at `/admin`.
