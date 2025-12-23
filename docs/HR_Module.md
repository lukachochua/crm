# HR Module Overview and Implementation

This document describes the HR module inside the CRM codebase. HR is a separate bounded context that integrates with shared infrastructure (users, permissions, audit logging) without CRM models referencing HR tables.

## Scope
- Employee Management: profiles tied 1:1 to users, org structure, contracts, and documents.
- KPI and Performance: templates, cycles, reports, and weighted score calculations.
- Training and Development: sessions, participants, and results.
- Recruitment: candidate pipeline tracking.
- Onboarding: templates, employee onboarding, and task progress.
- 360 Feedback: cycles, requests, answers, and aggregation inputs.
- Engagement Surveys: surveys, questions, submissions, and answers.

## Bounded Context Rules
- HR is a separate bounded context.
- HR models may reference `users.id` only.
- CRM models do not reference HR tables.
- Employees are always users: `employees.user_id` is a unique FK to `users.id`.
- Some employees never log in, but they still exist as users.
- No payroll or finance logic exists in HR.

## Architecture Fit With CRM
- Uses the same Eloquent conventions: domain models under `app/Models/Hr` and `app/Models/Crm`, soft deletes where required, and enum casts for status fields.
- Uses the same permission system: Spatie roles/permissions with centralized entity.action strings from `app/Support/Permissions.php`.
- Uses the same policy pattern: per-model policies and Filament visibility tied to `canViewAny`, `canCreate`, `canUpdate`, `canDelete`.
- Uses the same status transition enforcement approach: enums + `HasStatusTransitions` + `EnforcesStatusTransitions` where a status field exists.
- Uses the same audit log system: `AuditLogger` writes to the shared `audit_logs` table for status changes and deletions.
- Uses the existing notification infrastructure (database notifications, optional mail).

## Roles and Permissions (HR)
Legend: full = view/create/update/delete/export, view+update = view + update, view = view only.

| Capability | superadmin | hr_admin | hr_manager | department_manager |
| --- | --- | --- | --- | --- |
| Org Structure (departments, positions, branches, contract_types) | full | full | view | view |
| Employees | full | full | full | view |
| Employee Documents | full | full | full | view |
| KPI Templates | full | full | view | view |
| KPI Cycles | full | full | full | view |
| KPI Reports | full | full | full | view+update |
| Training Sessions | full | full | full | view |
| Training Participants | full | full | full | view+update |
| Candidates | full | full | full | view |
| Onboarding Templates | full | full | view | view |
| Employee Onboarding | full | full | full | view+update |
| Feedback Cycles | full | full | view | view |
| Feedback Requests | full | full | full | view+update |
| Engagement Surveys | full | full | full | view |
| Survey Submissions | full | full | full | view+update |

Policy-level scoping restricts hr_manager and department_manager access to their department/team records. Superadmin bypasses all scope checks.

## Status Transitions
- Employee: active -> suspended|left; suspended -> active|left; left terminal
- KPI Report: draft -> submitted -> manager_reviewed -> closed; closed terminal
- KPI Cycle: open -> closed
- Training Session: scheduled -> completed|cancelled; terminal after
- Training Attendance: invited -> confirmed -> attended|no_show|cancelled
- Training Result: pending -> passed|failed
- Recruitment Stage: application -> interview -> offer -> hired
- Onboarding: not_started -> in_progress -> completed; can cancel from not_started/in_progress
- Onboarding Task: pending -> in_progress -> completed; pending/in_progress -> blocked; blocked -> in_progress
- Feedback Cycle: draft -> open -> closed
- Feedback Request: pending -> submitted; pending -> cancelled; submitted -> closed
- Survey: draft -> open -> closed -> archived

## Folder Structure and Module Layout
- `app/Models/Hr/`
  - Core: `Employee`, `Department`, `Position`, `Branch`, `ContractType`, `EmployeeDocument`
  - KPI: `Kpi/` (template, items, cycle, report, report items)
  - Training: `Training/` (sessions, participants)
  - Recruitment: `Recruitment/` (candidates)
  - Onboarding: `Onboarding/` (templates, tasks, employee onboarding, employee tasks)
  - Feedback: `Feedback/` (cycle, questions, requests, answers)
  - Survey: `Survey/` (engagement survey, questions, submissions, answers)

- `app/Enums/Hr/`
  - Status enums with transitions: EmployeeStatus, KpiReportStatus, KpiCycleStatus, TrainingSessionStatus,
    TrainingAttendanceStatus, TrainingResultStatus, RecruitmentStage, OnboardingStatus, OnboardingTaskStatus,
    FeedbackCycleStatus, FeedbackRequestStatus, SurveyStatus
  - Non-status enums: RaterType, PeriodType, QuestionType

- `app/Policies/Hr/`
  - Policies for all HR models, plus `Concerns/ScopesHrAccess.php` for scope checks.

- `app/Observers/Hr/`
  - Observers for audit logging of status changes and deletions.
  - KpiReportItem observer recalculates KPI scores on save/delete.

- `app/Services/Hr/`
  - `KpiScoreCalculator` (weighted average score updates)
  - `HrReportService` (department/period KPI summaries)

- `app/Notifications/Hr/`
  - `ContractExpirationReminder`
  - `OnboardingDelayAlert`
  - `SurveyOpenCloseNotification`

- `app/Jobs/Hr/`
  - `SendContractExpirationReminders` (scheduled daily)
  - `SendOnboardingDelayAlerts` (scheduled daily)

- `app/Filament/Resources/Hr/`
  - Filament resources for all HR entities with relation managers for nested data.

- `database/migrations/`
  - HR migrations prefixed with `create_hr_` and timestamp ordered, covering all HR tables.

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

## Permissions and Role Behavior
Permissions are generated per entity/action (`view`, `create`, `update`, `delete`, `export`).
The HR roles are defined in the roles seeder and rely on policies for scope enforcement.

- `superadmin`: full access to all permissions (CRM + HR).
- `hr_admin`: full access to all HR entities.
- `hr_manager`: full HR operational access, view-only for org structure and templates where specified.
- `department_manager`: view-only in org/overview areas, view+update for KPI/onboarding/training/feedback/surveys.

### Scope Enforcement
Scope is enforced in policies (not permissions):
- `hr_admin` and `superadmin` bypass scope checks.
- `hr_manager` and `department_manager` are scoped by department and/or direct manager assignment.
- The scope check derives the manager's employee record and compares department or `manager_user_id`.

### Traits Used
- `app/Policies/Hr/Concerns/ScopesHrAccess.php`: shared HR policy scoping helpers.
- `app/Models/Concerns/EnforcesStatusTransitions.php`: enforces status transitions for HR status fields.
- `app/Models/Concerns/AssignsCreator.php`: sets `created_by` on engagement surveys.
- `app/Observers/Concerns/LogsDeletion.php`: standard deletion audit logging for HR observers.

## Audit Logging
Audit log entries are written to the shared `audit_logs` table using `AuditLogger`:
- Status changes for Employee, Candidate, KPI Report, Training Session, Onboarding, Onboarding Task, Feedback Cycle,
  Feedback Request, Engagement Survey.
- Deletions for core HR entities using `LogsDeletion` in observers.

Audit logging requires an authenticated user, consistent with CRM behavior.

## Notifications and Scheduling
Notifications use the existing database notification table and Laravel Notifications:

- Contract expiration reminders
  - Job: `SendContractExpirationReminders`
  - Trigger: daily schedule
  - Recipients: users with roles `superadmin`, `hr_admin`, `hr_manager`

- Onboarding delay alerts
  - Job: `SendOnboardingDelayAlerts`
  - Trigger: daily schedule
  - Recipients: HR roles plus the employee's assigned manager (if any)

- Survey open/close notifications
  - Trigger: EngagementSurvey status change to `open` or `closed`
  - Recipients: all active employees (via employee -> user)

Scheduling is configured in `routes/console.php` and uses the existing scheduler runtime.

## HR Functionality Flow
### Employee Management
- Create `users` first (via existing CRM user flow) then attach an `employees` record.
- Contract expiration is tracked via `contract_end_date`, with reminders sent by the scheduled job.
- Employee documents are stored with metadata and file path via Laravel filesystem.

### KPI and Performance
- KPI templates are defined per position, with weighted items.
- KPI cycles define reporting periods.
- KPI reports are created per employee/template/cycle, with self and manager scores.
- `KpiScoreCalculator` computes weighted totals and stores raw + computed scores.

### Training
- Training sessions define calendar events, optional trainer, and status.
- Participants track attendance status, result status, and optional score.

### Recruitment
- Candidates move through a strict pipeline: application -> interview -> offer -> hired.
- Stage transitions are enforced by enum validation on update.

### Onboarding
- Onboarding templates define reusable tasks.
- Employee onboarding tracks status and due dates.
- Task progress is tracked per employee onboarding; overdue tasks trigger alerts.

### 360 Feedback
- Feedback cycles define periods.
- Requests link a subject employee to a rater with a rater type.
- Answers store per-question scores and comments.

### Engagement Surveys
- Surveys define windows (opens/closes) and question sets.
- Submissions and answers are linked to users; anonymity is a reporting concern (not storage).

## Filament UI Integration
The HR module appears under a single "HR" navigation group. Resources follow CRM UI patterns
(forms, tables, relation managers) and are authorization-aware through policies.

HR roles (hr_admin, hr_manager, department_manager) land on a dedicated HR dashboard with
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
