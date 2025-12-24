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
The landing logic lives in `app/Filament/Pages/AdminLanding.php`.

## CRM Code Layout
- `app/Models/Crm/Sales`: applications, orders, reservations.
- `app/Models/Crm/Parties`: customers.
- `app/Models/Crm/Operations`: internal transfers and customer returns.
- `app/Models/Crm/Assets`: vehicles.
- `app/Models/Crm/Billing`: invoices, payments.
- `app/Models/Crm/Reporting`: turnover overview view model.
- `app/Observers/Crm`: audit logging and CRM side effects (auto-invoice).
- `app/Policies/Crm`: authorization policies.
- `app/Filament/Resources/Crm`: CRUD UI resources.
- `app/Filament/Widgets/Crm`: CRM dashboard widgets.
- `app/Filament/Exports/Crm`: export definitions.
- `app/Filament/Pages/Crm`: CRM dashboards.
- `app/Services/AuditLogger.php`: shared audit log writer.
- `app/Providers/AppServiceProvider.php`: observer registration and audit log morph map.
- `app/Providers/AuthServiceProvider.php`: policy registration.

## Role and Permission Matrix
Legend:
- view = read-only
- full = view + create + update + delete + export
- view+create = view + create
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

Additional CRM permissions:
- Customer Contracts and Pricing Profiles: Sales (view), Back Office/Admin (full).
- Internal Transfers and Customer Returns: Sales (view + create), Back Office/Admin (full).

## Data Model (Schema Overview)
### applications
- id, customer_id, status, requested_at, created_by, description, source, internal_notes, timestamps, deleted_at

### orders
- id, customer_id, application_id, order_number, status, total_amount, discount_amount, notes, created_by, timestamps, deleted_at

### reservations
- id, order_id, vehicle_id, status, reserved_from, reserved_until, notes, timestamps, deleted_at

### customers
- id, first_name, last_name, personal_id_or_tax_id, phone, email, address, notes, timestamps, deleted_at

### crm_customer_contracts
- id, customer_id, contract_number, contract_type, start_date, end_date, status, notes, created_by, timestamps, deleted_at

### crm_customer_pricing_profiles
- id, customer_id, pricing_type, discount_percent, currency_code, is_active, notes, created_by, timestamps, deleted_at

### vehicles
- id, vin_or_serial, type, status, model, year, color, notes, timestamps, deleted_at

### invoices
- id, order_id, invoice_number, status, total_amount, issued_at, due_date, notes, timestamps, deleted_at

### payments
- id, invoice_id, amount, status, payment_date, created_by, payment_method, reference_number, notes, timestamps, deleted_at

### internal_transfers
- id, reference, source_location, destination_location, description, status, requested_by, requested_at, notes, timestamps, deleted_at

### customer_returns
- id, reference, customer_id, description, status, received_at, reported_by, notes, timestamps, deleted_at

### customer_return_items (optional)
- id, customer_return_id, item_name, quantity, notes, timestamps

### audit_logs
- id, auditable_type, auditable_id, action_type, performed_by, performed_at, before_state, after_state, amount_before, amount_after, currency, notes, ip_address

### turnover_overviews (view)
- period (YYYY-MM), total_invoiced, total_paid, outstanding_amount
This view is read-only and not writable.

## Unified Document Registry
The CRM includes a read-only document registry that projects applications, orders, reservations, invoices, and payments into a single list. Each row links back to the original record view page. The registry is built via a query-based projection (`app/Services/Crm/DocumentRegistryQuery.php`) and exposed as a Filament resource (`app/Filament/Resources/Crm/CrmDocumentRegistryResource.php`). It respects the same view permissions as the underlying document types.

## Relationships (How Records Connect)
- Customer has many Applications and Orders.
- Customer has many Customer Contracts and Pricing Profiles.
- Application belongs to a Customer and may convert into an Order.
- Order belongs to a Customer and may reference an Application.
- Reservation belongs to an Order and a Vehicle.
- Invoice belongs to an Order.
- Payment belongs to an Invoice.
- TurnoverOverview is a database view from invoices and payments.
- CustomerReturn belongs to a Customer and includes optional CustomerReturnItems.
- InternalTransfer references the requesting user.

## Status Transitions
Status transitions and business gating rules are documented in `docs/Business_Processes.md`.
Enforcement is handled by enums and `EnforcesStatusTransitions` at the model layer.

## Amounts and Totals
Amounts are stored independently on Orders, Invoices, and Payments. Business implications
are documented in `docs/Business_Processes.md`.

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
Business rules (manual invoicing, auto-invoice, and settlement logic) are documented in
`docs/Business_Processes.md`. Implementation notes:
- Auto-invoice is triggered by `OrderObserver` when an order status changes to completed.
- Auto-invoices use status = draft, issued_at = now, due_date = null, and the note
  "Auto-created when order completed.".
- Invoice numbers use `INV-YYYYMMDD-####` with a uniqueness check.
- Auto-invoice warnings are shown in the web UI and suppressed in console contexts.
- Turnover Overview is a database view that aggregates total invoiced vs. total paid per month.

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
Audit logs use a polymorphic relation and are mapped in `app/Providers/AppServiceProvider.php` to preserve legacy class names.

### Current Assumptions and Defaults
- Order `total_amount` is the pre-discount price.
- Order `discount_amount` is an absolute amount (not a percentage).
- Discounts apply once at the order level, not per invoice.
- Multiple invoices per order are allowed; auto-invoice uses remaining balance.

### Known Limitations / Future Decisions
See `docs/CRM_Business_Guide.md` for open business decisions.

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
6) Access Filament at `/admin` (CRM users land at `/admin/crm`).
