# Business Processes (CRM + HR)

## Purpose
This document consolidates the business process rules, status gates, and automations for both
CRM and HR modules.

## Shared Operating Rules
- Status transitions are enforced by enums and blocked if invalid.
- Invalid transitions return a validation message in the UI.
- UI actions are hidden or disabled when policy checks fail.
- Most actions require an authenticated user; background jobs use system contexts.
- Audit logs record status changes and deletions when a user is present.
- Admin access starts at `/admin` with role-based routing to CRM or HR.
- List views support search, filters, and sorting.
- Status badges display the current state and color.

## CRM Processes

### End-to-End Flow (Real Life)
1) Customer inquiry -> Application.
2) Sales reviews and qualifies the request -> Application status changes.
3) Approved Application -> Convert to Order.
4) Order is confirmed and delivered -> status to Completed.
5) System may auto-create a draft Invoice for the remaining amount.
6) Back Office or Finance issues invoices and records Payments.
7) Turnover Overview aggregates monthly invoiced vs. paid totals.

### Record Rules and Status Gates
- Application
  - Status flow: new -> reviewed -> approved -> converted; reviewed -> rejected.
  - Meanings: new = intake logged; reviewed = qualified; approved = go-ahead; converted = order created;
    rejected = not pursued.
  - Conversion to Order is only allowed when approved, no order exists, user is signed in,
    and permissions allow order creation and application update.
- Order
  - Status flow: draft -> confirmed -> completed | cancelled.
  - Meanings: draft = prepared; confirmed = accepted; completed = delivered/finished; cancelled = closed.
  - Orders can exist without applications (`application_id` is optional).
  - Status change to completed triggers auto-invoice checks.
- Reservation
  - Status flow: active -> fulfilled | expired | cancelled.
  - Meanings: active = asset held; fulfilled = delivered/used; expired = lapsed; cancelled = voided.
  - Requires linked Order and Vehicle.
- Invoice
  - Status flow: draft -> issued -> partially_paid | paid | cancelled; partially_paid -> paid.
  - Meanings: draft = internal prep; issued = formally sent; partially_paid = partial settlement;
    paid = fully settled; cancelled = voided.
  - Multiple invoices per order are allowed; invoices can be created manually at any time.
- Payment
  - Status flow: pending -> completed | failed; completed -> reversed.
  - Meanings: pending = initiated; completed = received; reversed = refunded/chargeback;
    failed = not settled.
  - Only completed and reversed payments affect settlement calculations.
- Customer Contract
  - Status flow: active -> expired | terminated; expired -> terminated.
  - Tracks contract numbers, types, dates, and status per customer; metadata only.
- Customer Pricing Profile
  - Stores pricing type, discount, and currency metadata per customer; no calculations enforced.
- Internal Transfer
  - Status flow: draft -> submitted -> acknowledged -> closed; submitted -> cancelled.
  - Records intent to move items between locations; document-only record (no inventory side effects).
- Customer Return
  - Status flow: draft -> reported -> reviewed -> closed; reported -> cancelled.
  - Records a return notice with optional item details; document-only record (no inventory side effects).
- Unified Document Registry
  - Read-only list of applications, orders, reservations, invoices, and payments; opens the
    source record and respects view permissions.
- Turnover Overview
  - Read-only monthly aggregation of invoiced vs. paid totals.

### CRM Automations and Calculations
- Convert to Order
  - Conditions: application status = approved, no existing order, authenticated user,
    and permissions allow `orders.create` and `applications.update`.
  - Effects: creates Order, sets Application status to converted, redirects to Order.
- Auto-invoice on Order completion
  - Trigger: Order status changes to completed.
  - Billable total: `max(0, order.total_amount - order.discount_amount)`.
  - Remaining: `billable_total - sum(existing invoices)`.
  - Skips if remaining <= 0 or if fully paid and already invoiced.
  - Fully paid check: `sum(completed payments) - sum(reversed payments) >= billable_total`.
  - Defaults on auto-invoice: status = draft, issued_at = now, due_date = null,
    notes = "Auto-created when order completed.".
- Amount independence
  - Order totals, invoice totals, and payment amounts are stored independently. This supports
    partial invoicing and partial payments.
  - Invoice totals and dates can be set or adjusted manually by permitted roles.
- Exports
  - Export actions appear only if the role has export permissions. Exports run via queued jobs
    and notify the user when ready.

### CRM Role Touchpoints
Role responsibilities and step-by-step usage live in `docs/CRM_Business_Guide.md`.

## HR Processes

### Record Rules and Status Gates
- Employee status: active -> suspended | left; suspended -> active | left; left terminal.
- KPI cycle status: open -> closed.
- KPI report status: draft -> submitted -> manager_reviewed -> closed.
- Training session status: scheduled -> completed | cancelled.
- Training attendance status: invited -> confirmed -> attended | no_show | cancelled.
- Training result status: pending -> passed | failed.
- Recruitment stage: application -> interview -> offer -> hired.
- Onboarding status: not_started -> in_progress -> completed; cancel from not_started/in_progress.
- Onboarding task status: pending -> in_progress | blocked; in_progress -> completed | blocked;
  blocked -> in_progress.
- Feedback cycle status: draft -> open -> closed.
- Feedback request status: pending -> submitted | cancelled; submitted -> closed.
- Survey status: draft -> open -> closed -> archived.

### HR Operational Flows
- Employee lifecycle
  - Create a User first, then attach an Employee profile.
  - Track contract end dates and status changes.
  - Employee documents are stored with metadata and file paths.
- KPI and performance
  - Templates are defined per position with weighted items.
  - KPI templates -> KPI cycles (reporting periods) -> KPI reports per employee/template/cycle.
  - Reports carry self and manager scores; totals are recalculated when items change.
- Training
  - Training sessions define calendar events and optional trainer.
  - Participants track attendance, result status, and optional score.
- Recruitment
  - Candidates move through the stage pipeline with enforced transitions.
- Onboarding
  - Templates define reusable tasks.
  - Employee onboarding tracks due dates and status.
  - Task progress is tracked per onboarding record; overdue onboarding triggers alerts.
- Feedback
  - Feedback cycles define periods.
  - Requests link subject employees and raters with a rater type.
  - Answers capture per-question scores and comments.
- Engagement surveys
  - Surveys define open/close windows and question sets.
  - Submissions and answers are linked to users; anonymity is a reporting concern (not storage).
  - No payroll or finance logic exists in HR.

### HR Notifications and Scheduling
- Contract expiration reminders
  - Daily schedule; targets contracts expiring within 30 days.
  - Recipients: users with roles `superadmin`, `hr_admin`, `hr_manager`.
- Onboarding delay alerts
  - Daily schedule; targets overdue onboarding in not_started/in_progress.
  - Recipients: HR roles plus the employee's manager (if assigned).
- Survey open/close notifications
  - Triggered when survey status changes to open or closed.
  - Recipients: all active employees (via employee -> user).

### HR Access Scoping
- `superadmin` and `hr_admin` bypass scope checks.
- `hr_manager` and `department_manager` are scoped by department or direct manager assignment.
- Policy helpers live in `app/Policies/Hr/Concerns/ScopesHrAccess.php`.

Role responsibilities and navigation details live in `docs/HR_Module.md`.
