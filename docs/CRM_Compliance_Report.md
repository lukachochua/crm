# CRM Compliance Report

## Baseline
- Source of truth: docs/CRM.md
- Scope: Laravel 12 CRM implementation in this repository

## Summary
Overall alignment is high. Core entities, enums, relationships, permissions, and audit logging match the approved spec. A few implementation details go beyond the spec (exports support) and a couple of behaviors are implicit rather than explicitly enforced (turnover calculations by status, audit log immutability).

## Compliance Matrix

### System Architecture / Core Stack
Compliant.
- Laravel 12 + Filament v3, Spatie permissions, policies: see composer.json and app/Providers/Filament/AdminPanelProvider.php

### Entities and Tables
Compliant.
- applications: database/migrations/2025_12_19_190910_create_applications_table.php
- orders: database/migrations/2025_12_19_190915_create_orders_table.php
- reservations: database/migrations/2025_12_19_190920_create_reservations_table.php
- customers: database/migrations/2025_12_19_190900_create_customers_table.php
- vehicles: database/migrations/2025_12_19_190905_create_vehicles_table.php
- invoices: database/migrations/2025_12_19_190925_create_invoices_table.php
- payments: database/migrations/2025_12_19_190930_create_payments_table.php
- audit_logs: database/migrations/2025_12_19_190940_create_audit_logs_table.php
- turnover_overviews view: database/migrations/2025_12_19_191200_create_turnover_overviews_view.php

### Enums and Status Transitions
Compliant.
- Application: app/Enums/ApplicationStatus.php
- Order: app/Enums/OrderStatus.php
- Reservation: app/Enums/ReservationStatus.php
- Invoice: app/Enums/InvoiceStatus.php
- Payment: app/Enums/PaymentStatus.php
- Guard enforcement: app/Models/Concerns/EnforcesStatusTransitions.php

### Role / Permission Matrix
Compliant.
- Permissions list: app/Support/Permissions.php
- Role mapping: database/seeders/RolesAndPermissionsSeeder.php

### Policies
Compliant.
- Policies for all entities: app/Policies/*Policy.php
- Registered in app/Providers/AuthServiceProvider.php

### Audit Logging
Compliant.
- Status changes: app/Observers/ApplicationObserver.php, app/Observers/OrderObserver.php, app/Observers/ReservationObserver.php, app/Observers/InvoiceObserver.php, app/Observers/PaymentObserver.php
- Payments: app/Observers/PaymentObserver.php
- Deletions: app/Observers/Concerns/LogsDeletion.php and observers
- Audit storage: app/Models/AuditLog.php, app/Services/AuditLogger.php

### Filament Resources
Compliant.
- Resources: app/Filament/Resources/*Resource.php
- Relation managers: app/Filament/Resources/CustomerResource/RelationManagers/OrdersRelationManager.php, app/Filament/Resources/OrderResource/RelationManagers/InvoicesRelationManager.php
- Navigation hidden when no access: shouldRegisterNavigation uses canViewAny
- Read-only roles: create/edit/delete actions hidden via Gate checks

### Turnover Overview
Compliant.
- Read-only view and resource: database/migrations/2025_12_19_191200_create_turnover_overviews_view.php, app/Models/TurnoverOverview.php, app/Filament/Resources/TurnoverOverviewResource.php

## Deviations / Additions

### Additional Export Support (Not in CRM.md)
Added to support Filament export functionality.
- Exporters: app/Filament/Exports/*.php
- Exports table migration: database/migrations/2025_12_19_191300_create_exports_table.php
- Notifications table migration: database/migrations/2025_12_19_191305_create_notifications_table.php

### Vehicle Status Values
CRM.md does not define a vehicle status enum. The UI uses specific values:
- available, reserved, sold
- Implemented in app/Filament/Resources/VehicleResource.php

## Behavioral Notes (Not Explicitly Specified)
- Turnover view sums all invoices and payments that are not soft-deleted, without filtering by status. If turnover should exclude draft or cancelled invoices, or failed/reversed payments, this is a spec clarification needed.
- Audit logs are not exposed via UI and are not soft-deleted, but immutability is not explicitly enforced by model guards.

## Overall Fit
The implementation matches the approved architecture and data model closely, with minor additions required by Filament export mechanics and minor assumptions around vehicle status values and turnover aggregation rules.
