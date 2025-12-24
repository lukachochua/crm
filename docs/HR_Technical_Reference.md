# HR Technical Reference

## Purpose
This document is the technical source of truth for the HR module: architecture, data model, permissions, runtime behavior, and operational flows.

## Architecture Overview
- HR is a separate bounded context inside the CRM codebase.
- HR models reference `users.id` only; CRM models do not reference HR tables.
- Employees are always users (`employees.user_id` is unique).
- No payroll or finance logic exists in HR.
- Uses shared infrastructure: permissions, policies, audit logging, notifications.

## Admin Panel Routing
- `/admin` redirects HR roles to `/admin/hr`.
- HR dashboard lives under `app/Filament/Pages/Hr`.

## HR Code Layout
- `app/Models/Hr`: HR domain models.
- `app/Enums/Hr`: HR enums and status transitions.
- `app/Policies/Hr`: HR authorization policies and scoping helpers.
- `app/Observers/Hr`: audit logging and recalculation hooks.
- `app/Services/Hr`: KPI score calculation and reporting helpers.
- `app/Jobs/Hr` and `app/Notifications/Hr`: scheduled reminders and alerts.
- `app/Filament/Resources/Hr`: HR CRUD resources and relation managers.
- `app/Filament/Widgets/Hr`: HR dashboard widgets.

## Data Model and Relationships
### Employee Management
- `employees` belongsTo `users` (1-1) via `user_id`.
- `employees` belongsTo `departments`, `positions`, `branches`, `contract_types`.
- `employees` belongsTo `users` as `manager` via `manager_user_id`.
- `employee_documents` belongsTo `employees` and belongsTo `users` as `uploaded_by`.

### KPI and Performance
- `kpi_templates` belongsTo `positions` and hasMany `kpi_template_items`.
- `kpi_cycles` hasMany `kpi_reports`.
- `kpi_reports` belongsTo `employees`, `kpi_templates`, and `kpi_cycles`.
- `kpi_report_items` belongsTo `kpi_reports` and `kpi_template_items`.

### Training and Development
- `training_sessions` hasMany `training_participants`.
- `training_sessions` belongsTo `users` as `trainer` (nullable).
- `training_participants` belongsTo `training_sessions` and `employees`.

### Recruitment
- `candidates` belongsTo `positions` and `branches` (nullable).

### Onboarding
- `onboarding_templates` belongsTo `departments` and `positions` (nullable).
- `onboarding_templates` hasMany `onboarding_template_tasks`.
- `employee_onboardings` belongsTo `employees` and `onboarding_templates`.
- `employee_onboarding_tasks` belongsTo `employee_onboardings` and `onboarding_template_tasks`.
- `employee_onboarding_tasks` belongsTo `users` as assignee (nullable).

### 360 Feedback
- `feedback_cycles` hasMany `feedback_questions` and `feedback_requests`.
- `feedback_requests` belongsTo `feedback_cycles` and `employees` (subject) and `users` (rater).
- `feedback_answers` belongsTo `feedback_requests` and `feedback_questions`.

### Engagement Surveys
- `engagement_surveys` belongsTo `users` (created_by), hasMany `survey_questions` and `survey_submissions`.
- `survey_submissions` belongsTo `engagement_surveys` and `users`.
- `survey_answers` belongsTo `survey_submissions` and `survey_questions`.

## Status Transitions and Enforcement
Status transitions and business gating rules are documented in `docs/Business_Processes.md`.
This section describes how enforcement works in code.

All status enums use `HasStatusTransitions`. Models with status fields use
`EnforcesStatusTransitions` where possible. For non-standard field names
(for example `stage`, `attendance_status`), transitions are validated in model
`booted` hooks.

## Permissions and Role Behavior
Permissions are generated per entity/action (`view`, `create`, `update`, `delete`, `export`).

Roles:
- `superadmin`: full access to all permissions (CRM + HR).
- `hr_admin`: full access to all HR entities.
- `hr_manager`: full HR operational access, view-only for org structure and templates where specified.
- `department_manager`: view-only in org/overview areas, view+update for KPI/onboarding/training/feedback/surveys.

### Scope Enforcement
Scope is enforced in policies (not permissions):
- `hr_admin` and `superadmin` bypass scope checks.
- `hr_manager` and `department_manager` are scoped by department and/or direct manager assignment.
- The scope check derives the manager's employee record and compares department or `manager_user_id`.

### Policy Helpers
- `app/Policies/Hr/Concerns/ScopesHrAccess.php`: shared HR policy scoping helpers.

## Audit Logging
Audit log entries are written to the shared `audit_logs` table using `AuditLogger`:
- Status changes for Employee, Candidate, KPI Report, Training Session, Onboarding, Onboarding Task, Feedback Cycle,
  Feedback Request, Engagement Survey.
- Deletions for core HR entities using `LogsDeletion` in observers.

Audit logging requires an authenticated user, consistent with CRM behavior.

## Services and Calculations
- `KpiScoreCalculator` computes weighted totals and stores `self_score_total`, `manager_score_total`, and `computed_score`.
- `HrReportService` provides KPI summaries by department and by period (month/quarter/year depending on cycle settings).

## Notifications and Scheduling
Business rules (recipients and triggers) are documented in `docs/Business_Processes.md`.
Implementation details:

- Contract expiration reminders
  - Job: `SendContractExpirationReminders`
  - Trigger: daily schedule
- Onboarding delay alerts
  - Job: `SendOnboardingDelayAlerts`
  - Trigger: daily schedule
- Survey open/close notifications
  - Trigger: EngagementSurvey status change to `open` or `closed`

Scheduling is configured in `routes/console.php` and uses the existing scheduler runtime.

## HR Functionality Implementation Notes
Operational flows and status gates are documented in `docs/Business_Processes.md`.
Implementation highlights include:
- KPI score recalculation via `KpiScoreCalculator` triggered by `KpiReportItemObserver`.
- Audit logging via `AuditLogger` in HR observers.

## Filament UI Integration
The HR module appears under a single "HR" navigation group. Resources follow CRM UI patterns
(forms, tables, relation managers) and are authorization-aware through policies.

HR roles (`hr_admin`, `hr_manager`, `department_manager`) land on a dedicated HR dashboard with
HR widgets and do not see the standard CRM dashboard. Superadmin keeps the standard CRM
dashboard and can still access HR resources.

Order (navigationSort):
- Employees
- Org Structure (Departments, Positions, Branches, Contract Types)
- Documents
- Performance (KPI Templates, KPI Cycles, KPI Reports)
- Training (Sessions, Participants)
- Recruitment (Candidates)
- Onboarding (Templates, Employee Onboarding)
- Feedback (Cycles, Requests)
- Surveys (Engagement Surveys, Survey Submissions)

## Implementation Notes
- Status transitions are enforced at the model layer, not only in UI.
- Policy scoping is strict for hr_manager and department_manager roles.
- CRM domain remains independent; there are no HR references in CRM models.
