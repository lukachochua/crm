# CRM Compliance Report

## Baseline
- Source of truth: docs/CRM_Technical_Reference.md
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
- Application: app/Enums/Crm/ApplicationStatus.php
- Order: app/Enums/Crm/OrderStatus.php
- Reservation: app/Enums/Crm/ReservationStatus.php
- Invoice: app/Enums/Crm/InvoiceStatus.php
- Payment: app/Enums/Crm/PaymentStatus.php
- Guard enforcement: app/Models/Concerns/EnforcesStatusTransitions.php

### Role / Permission Matrix
Compliant.
- Permissions list: app/Support/Permissions.php
- Role mapping: database/seeders/RolesAndPermissionsSeeder.php

### Policies
Compliant.
- Policies for all entities: app/Policies/Crm/*Policy.php
- Registered in app/Providers/AuthServiceProvider.php

### Audit Logging
Compliant.
- Status changes: app/Observers/Crm/ApplicationObserver.php, app/Observers/Crm/OrderObserver.php, app/Observers/Crm/ReservationObserver.php, app/Observers/Crm/InvoiceObserver.php, app/Observers/Crm/PaymentObserver.php
- Payments: app/Observers/Crm/PaymentObserver.php
- Deletions: app/Observers/Concerns/LogsDeletion.php and observers
- Audit storage: app/Models/AuditLog.php, app/Services/AuditLogger.php

### Filament Resources
Compliant.
- Resources: app/Filament/Resources/Crm/*Resource.php
- Relation managers: app/Filament/Resources/Crm/CustomerResource/RelationManagers/OrdersRelationManager.php, app/Filament/Resources/Crm/OrderResource/RelationManagers/InvoicesRelationManager.php
- Navigation hidden when no access: shouldRegisterNavigation uses canViewAny
- Read-only roles: create/edit/delete actions hidden via Gate checks

### Turnover Overview
Compliant.
- Read-only view and resource: database/migrations/2025_12_19_191200_create_turnover_overviews_view.php, app/Models/Crm/Reporting/TurnoverOverview.php, app/Filament/Resources/Crm/TurnoverOverviewResource.php

## Deviations / Additions

### Additional Export Support (Not in CRM_Technical_Reference.md)
Added to support Filament export functionality.
- Exporters: app/Filament/Exports/Crm/*.php
- Exports table migration: database/migrations/2025_12_19_191300_create_exports_table.php
- Notifications table migration: database/migrations/2025_12_19_191305_create_notifications_table.php

### Vehicle Status Values
CRM_Technical_Reference.md does not define a vehicle status enum. The UI uses specific values:
- available, reserved, sold
- Implemented in app/Filament/Resources/Crm/VehicleResource.php

## Behavioral Notes (Not Explicitly Specified)
- Turnover view sums all invoices and payments that are not soft-deleted, without filtering by status. If turnover should exclude draft or cancelled invoices, or failed/reversed payments, this is a spec clarification needed.
- Audit logs are not exposed via UI and are not soft-deleted, but immutability is not explicitly enforced by model guards.

## Overall Fit
The implementation matches the approved architecture and data model closely, with minor additions required by Filament export mechanics and minor assumptions around vehicle status values and turnover aggregation rules.

---

# HR Compliance Matrix (Based on HR Requirements List)

The following matrix maps the HR requirements list to the current implementation.
Technical details for HR live in `docs/HR_Technical_Reference.md`.

## Status Legend
- Compliant: Implemented and available in the UI or via scheduled jobs.
- Partial: Implemented at the model/service layer but not fully surfaced or missing some reporting.
- Gap: Not implemented.

## HR Requirements Matrix

| Requirement | Status | Evidence (Files) |
| --- | --- | --- |
| 1.1 Employee profiles with name, position, department, branch, contract type, start date, status | Compliant | app/Models/Hr/Employee.php, app/Filament/Resources/Hr/EmployeeResource.php, database/migrations/2026_01_10_090040_create_hr_employees_table.php |
| 1.2 Documents attached to employee profiles | Compliant | app/Models/Hr/EmployeeDocument.php, app/Filament/Resources/Hr/EmployeeDocumentResource.php, app/Filament/Resources/Hr/EmployeeResource/RelationManagers/EmployeeDocumentsRelationManager.php |
| 1.3 Contract expiration notifications | Compliant | app/Jobs/Hr/SendContractExpirationReminders.php, app/Notifications/Hr/ContractExpirationReminder.php, routes/console.php |
| 2.1 KPI template tied to position | Compliant | app/Models/Hr/Kpi/KpiTemplate.php, app/Filament/Resources/Hr/KpiTemplateResource.php |
| 2.2 Employee self input + manager evaluation | Compliant | app/Models/Hr/Kpi/KpiReport.php, app/Filament/Resources/Hr/KpiReportResource.php, app/Filament/Resources/Hr/KpiReportResource/RelationManagers/KpiReportItemsRelationManager.php |
| 2.3 Automatic score/status calculation | Compliant | app/Services/Hr/KpiScoreCalculator.php, app/Observers/Hr/KpiReportItemObserver.php |
| 2.4 Department/month/quarter reporting | Partial | app/Services/Hr/HrReportService.php (service exists but not surfaced in UI) |
| 3.1 Training calendar (date, topic, participants, status) | Compliant | app/Models/Hr/Training/TrainingSession.php, app/Filament/Resources/Hr/TrainingSessionResource.php, app/Filament/Widgets/Hr/UpcomingTrainingSessionsWidget.php |
| 3.2 Training participation and results per employee | Compliant | app/Models/Hr/Training/TrainingParticipant.php, app/Filament/Resources/Hr/TrainingParticipantResource.php |
| 4.1 Candidate database by position | Compliant | app/Models/Hr/Recruitment/Candidate.php, app/Filament/Resources/Hr/CandidateResource.php |
| 4.2 Recruitment stage control (application/interview/offer/hired) | Compliant | app/Enums/Hr/RecruitmentStage.php, app/Models/Hr/Recruitment/Candidate.php |
| 4.3 Recruitment analytics (status monitoring) | Partial | app/Filament/Widgets/Hr/RecruitmentPipelineChart.php (stage counts only) |
| 5.1 Onboarding tasks list, status, remaining, overdue | Compliant | app/Models/Hr/Onboarding/EmployeeOnboarding.php, app/Models/Hr/Onboarding/EmployeeOnboardingTask.php, app/Filament/Resources/Hr/EmployeeOnboardingResource.php, app/Filament/Widgets/Hr/OverdueOnboardingWidget.php |
| 5.2 Notifications for onboarding changes | Partial | app/Jobs/Hr/SendOnboardingDelayAlerts.php, app/Notifications/Hr/OnboardingDelayAlert.php (overdue only) |
| 6.1 360 Feedback process (manager, peers, self) | Compliant | app/Models/Hr/Feedback/FeedbackCycle.php, app/Models/Hr/Feedback/FeedbackRequest.php, app/Models/Hr/Feedback/FeedbackQuestion.php, app/Models/Hr/Feedback/FeedbackAnswer.php, app/Filament/Resources/Hr/FeedbackCycleResource.php, app/Filament/Resources/Hr/FeedbackRequestResource.php |
| 6.2 Feedback results stored on employee profile and shown on evaluation field | Gap | app/Models/Hr/Employee.php (fields exist but no aggregation or UI exposure) |
| 7.1 Engagement survey module with automatic reporting | Partial | app/Models/Hr/Survey/EngagementSurvey.php, app/Models/Hr/Survey/SurveySubmission.php, app/Models/Hr/Survey/SurveyAnswer.php, app/Filament/Resources/Hr/EngagementSurveyResource.php (no dedicated analytics/reporting) |

## HR Gaps and Follow-Ups
- 2.4 KPI reporting exists as a service but is not surfaced in the UI.
- 4.3 Recruitment analytics are limited to stage counts; no closed-position metrics or time-to-fill reporting.
- 5.2 Onboarding notifications cover overdue status only; no general change notifications.
- 6.2 Feedback result aggregation into employee profiles is not implemented in services or UI.
- 7.1 Engagement survey reporting exists as raw submissions but lacks automated analytics or summary views.
