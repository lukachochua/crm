# CRM Developer Onboarding Guide

Welcome to the CRM codebase. This guide helps junior and mid-level developers
understand the architecture, project structure, and day-to-day workflows.

## Quick Start (Local Setup)
1) Install dependencies:
   - `composer install`
   - `npm install`
2) Configure `.env` (database credentials; default DB name is `crm`).
3) Run migrations and seed roles:
   - `php artisan migrate`
   - `php artisan db:seed`
4) Create a Filament admin user:
   - `php artisan make:filament-user`
5) Run the app:
   - `php artisan serve`
   - `npm run dev`
6) Visit the admin panel at `/admin`.

Tip: `composer run dev` runs server, queue, logs, and Vite together.

## What This System Does (Business Overview)
The CRM models the commercial flow:
1) Application (customer request)
2) Order (commercial commitment)
3) Reservation (asset allocation)
4) Invoice (billing)
5) Payment (cash movement)
6) Turnover overview (monthly invoiced vs paid)

These steps are enforced with status enums and transition rules. Most actions
are audited and access-controlled by role permissions.

## Project Structure (Where Things Live)
- `app/Models`: Eloquent models and relationships.
- `app/Enums`: Status enums and transition rules.
- `app/Models/Concerns`: Shared model behaviors (status transitions, created_by).
- `app/Observers`: Audit logging and domain side effects.
- `app/Policies`: Authorization policies per entity.
- `app/Filament/Resources`: CRUD UI for each entity.
- `app/Filament/Widgets`: Dashboard widgets and charts.
- `app/Filament/Exports`: Export definitions for CSV/Excel.
- `app/Providers/Filament/AdminPanelProvider.php`: Filament panel config.
- `database/migrations`: Schema and turnover view.
- `database/seeders`: Roles and permissions seeding.
- `docs`: Business and runtime documentation.

## Core Domain Models and Relationships
- Customer has many Applications and Orders.
- Application belongs to Customer and may convert into an Order.
- Order belongs to Customer and may reference an Application.
- Reservation belongs to Order and Vehicle.
- Invoice belongs to Order.
- Payment belongs to Invoice.
- TurnoverOverview is a database view from invoices and payments.
- AuditLog records immutable system events.

## Statuses and Transition Rules
Status changes are validated at the model level. Invalid transitions fail even
if the UI attempts them.

- Applications: new -> reviewed -> approved -> converted; reviewed -> rejected
- Orders: draft -> confirmed -> completed or cancelled
- Reservations: active -> fulfilled / expired / cancelled
- Invoices: draft -> issued -> partially_paid -> paid; issued -> cancelled
- Payments: pending -> completed -> reversed; pending -> failed

Status enforcement is handled in `app/Models/Concerns/EnforcesStatusTransitions.php`
and the enum classes in `app/Enums`.

## Authorization and Permissions
This project uses Spatie Laravel Permission and Laravel Policies.

- Permissions are generated in `app/Support/Permissions.php`.
- Roles are seeded in `database/seeders/RolesAndPermissionsSeeder.php`.
- Each Policy checks permissions like `orders.view` or `invoices.update`.
- Filament hides actions and navigation when checks fail.

Common roles:
- Sales, Back Office, Finance, Turnover, Admin.

## Filament UI Architecture
Filament is the UI layer and defines:
- Forms (create/edit) and tables (list) per Resource.
- Custom actions (e.g., Application -> Order conversion).
- Dashboard widgets (KPIs, charts, and recent activity).

Panel configuration lives in:
`app/Providers/Filament/AdminPanelProvider.php`

## Key Runtime Behaviors
### Audit Logging
Observers write immutable audit log entries on:
- Status changes
- Financial changes
- Deletions

Audit logging requires an authenticated user. Running certain actions in console
without an authenticated user can throw exceptions.

### Auto-Invoice on Order Completion
When an Order status changes to `completed`, the system attempts to create a
draft invoice for the remaining billable amount. If the order is already fully
paid or fully invoiced, it skips and shows a warning.

Logic lives in `app/Observers/OrderObserver.php`.

### Turnover Overview (Read-Only View)
`turnover_overviews` is a database view that aggregates monthly invoiced vs paid
totals. It is not writable; treat it as read-only.

## Exports and Background Jobs
- Filament exports create an export record and run a queued job.
- A queue worker should be running in dev for exports to finish.
- Exporters are in `app/Filament/Exports`.

## Development Workflow Tips
- Prefer updates via Resources/Models, not direct DB changes.
- Respect status transition rules; bypassing them is a bug.
- Use seeders for roles/permissions; do not hand-edit permissions in code.
- Avoid direct writes to audit logs; use the `AuditLogger` service.
- Keep UI behavior aligned with Policy checks.

## Common Commands
- `php artisan migrate`
- `php artisan db:seed --class=RolesAndPermissionsSeeder`
- `php artisan make:filament-user`
- `php artisan permission:cache-reset`
- `php artisan filament:clear-cached-components` (if widgets/resources don’t appear)
- `composer run dev` (server + queue + logs + Vite)

## Testing
There are currently no significant automated tests in `tests/`. When adding new
critical business logic, add targeted unit or feature tests.

## Troubleshooting
- Widgets or resources missing: clear Filament component cache.
- Permissions not applying: run permission cache reset.
- Audit logger errors: ensure actions happen under an authenticated user.
- Local URL issues in dev: `AppServiceProvider` forces the root URL for local.

## Glossary
- Application: customer request / intake.
- Order: commercial commitment.
- Reservation: asset allocation window.
- Invoice: billing record.
- Payment: cash movement.
- Turnover overview: monthly financial summary.

## Additional Docs
See `docs/CRM.md`, `docs/CRM_Runtime_Flow.md`, `docs/Invoicing.md`,
`docs/CRM_User_Manual.md`, and `docs/CRM_CEO_Overview.md`.
