# AGENTS.md

## Purpose
This file orients coding agents to the CRM codebase: what to touch, how workflows are enforced,
and where to find authoritative documentation.

## Quick Orientation
- Admin UI is Filament v3 at `/admin` with role-based routing to `/admin/crm` or `/admin/hr`.
- CRM and HR are separate bounded contexts under `app/Models/Crm` and `app/Models/Hr`.
- Status transitions are enforced by enums and model guards; invalid jumps are blocked.
- Policies and Spatie permissions gate UI visibility and actions.

## Authoritative Docs
- Business rules and status gates: `docs/Business_Processes.md`.
- CRM technical details: `docs/CRM_Technical_Reference.md`.
- HR overview and permissions: `docs/HR_Module.md`.
- HR technical details: `docs/HR_Technical_Reference.md`.
- Developer setup and structure: `docs/Developer_Guide.md`.

## Entry Points and Routing
- Admin landing: `app/Filament/Pages/AdminLanding.php`.
- Panel config: `app/Providers/Filament/AdminPanelProvider.php`.
- CRM dashboard: `app/Filament/Pages/Crm/CrmDashboard.php`.
- HR dashboard: `app/Filament/Pages/Hr/HrDashboard.php`.

## Key Patterns
- UI: Filament resources under `app/Filament/Resources/{Crm,Hr}`; widgets under `app/Filament/Widgets/*`.
- Policies: `app/Policies/Crm` and `app/Policies/Hr` with role/permission checks.
- Permissions: `app/Support/Permissions.php` defines entity/action strings used by policies.
- Status enforcement:
  - `app/Models/Concerns/EnforcesStatusTransitions.php` for `status` fields.
  - Non-standard fields (e.g., `stage`, `attendance_status`) enforce transitions in model `booted` hooks.
- Audit logging: `app/Services/AuditLogger.php` with domain observers in `app/Observers/*`.

## CRM Workflow Hotspots
- Application -> Order conversion: `app/Filament/Resources/Crm/ApplicationResource/Pages/Concerns/ConvertsApplicationToOrder.php`.
- Auto-invoice on order completion: `app/Observers/Crm/OrderObserver.php`.
- Status enums: `app/Enums/Crm/*`.
- Turnover view model: `app/Models/Crm/Reporting/TurnoverOverview.php`.

## HR Workflow Hotspots
- Status enums: `app/Enums/Hr/*`.
- HR scoping helpers: `app/Policies/Hr/Concerns/ScopesHrAccess.php`.
- KPI scoring: `app/Services/Hr/KpiScoreCalculator.php`.
- Scheduled notifications:
  - Contract reminders: `app/Jobs/Hr/SendContractExpirationReminders.php`.
  - Onboarding alerts: `app/Jobs/Hr/SendOnboardingDelayAlerts.php`.

## Roles and Seeding
- Seed roles and permissions via `database/seeders/RolesAndPermissionsSeeder.php`.
- Policies are registered in `app/Providers/AuthServiceProvider.php`.

## Common Dev Commands
- Install: `composer install`, `npm install`.
- Migrate + seed: `php artisan migrate`, `php artisan db:seed`.
- Create admin user: `php artisan make:filament-user`.
- Run dev: `composer run dev` (server, queue, logs, Vite).

## When Adding a New Module
- Add models, enums, observers, policies, and Filament resources/pages/widgets.
- Register dashboards in `app/Providers/Filament/AdminPanelProvider.php` and update
  `app/Filament/Pages/AdminLanding.php` if routing changes.
- Update documentation in `docs/` (see `docs/Developer_Guide.md`).
