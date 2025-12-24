# Manual Test Guide (Temporary)

This document is a comprehensive, role-by-role user manual for manual testing.
It covers CRM and HR business flows, role permissions, and step-by-step test
procedures. Remove this file after manual testing is complete.

## Roles Covered (All Known Roles)
- superadmin
- Admin
- Sales
- Back Office
- Finance
- Turnover
- hr_admin
- hr_manager
- department_manager

## Common Access and UI Notes
- Login URL: `/admin`.
- Role-based landing:
  - CRM roles -> `/admin/crm`
  - HR roles -> `/admin/hr`
  - superadmin -> CRM dashboard but can access HR resources
- Navigation groups (CRM):
  - Sales: Applications, Orders, Reservations
  - Clients & Assets: Customers, Vehicles
  - Invoicing: Invoices
  - Finance: Payments
  - Turnover: Turnover Overview
  - Operations: Internal Transfers, Customer Returns, Unified Document Registry
- Navigation groups (HR):
  - People: Employees, Employee Documents, Contract Types
  - Org Structure: Branches, Departments, Positions
  - Recruitment: Candidates
  - Onboarding: Templates, Employee Onboarding
  - Performance: KPI Templates, KPI Cycles, KPI Reports
  - Training: Sessions, Participants
  - Feedback: Cycles, Requests
  - Surveys: Engagement Surveys, Survey Submissions
- Status transitions are strictly enforced; invalid jumps are blocked.
- Audit logs are recorded for status changes and deletions.
- Exports require a queue worker; notifications require scheduler.

## Global Test Prerequisites
- Create users for each role listed above and assign roles.
- Seed or manually create baseline data:
  - CRM: at least 2 Customers, 2 Vehicles, 1 Application, 1 Order, 1 Reservation.
  - HR: 2 Branches, 2 Departments, 2 Positions, Contract Types, 3 Users,
    and Employee profiles (at least 1 manager with reports).
- Confirm role-based navigation visibility before running test steps.

## Business Flows (High-Level)

### CRM End-to-End Flow
1) Application (customer request)
2) Order (commercial commitment)
3) Reservation (asset allocation)
4) Invoice (billing document)
5) Payment (cash collection)
6) Turnover Overview (monthly invoiced vs paid)

Variants:
- Orders can be created without Applications.
- Invoices can be created manually; multiple invoices per order are allowed.
- Payments are recorded against invoices; partial payments are supported.
- Internal Transfers and Customer Returns are document-only.

### HR End-to-End Flows
- Employee lifecycle: User -> Employee profile -> status changes and documents.
- Performance: KPI Templates -> Cycles -> Reports -> weighted scoring.
- Training: Sessions -> Participants -> attendance and results.
- Recruitment: Candidates progress through stages.
- Onboarding: Templates -> Employee Onboarding -> tasks and overdue checks.
- Feedback: Cycles -> Requests -> Answers.
- Engagement surveys: Surveys -> Submissions -> Answers.

## CRM Role Manuals and Test Steps

### Sales Role (full CRM intake + conversion)
1) Customers
   - Go to Clients & Assets -> Customers -> Create.
   - Required fields: first_name, last_name, personal_id_or_tax_id, phone.
   - Save and confirm record appears in list.
2) Vehicles
   - Go to Clients & Assets -> Vehicles -> Create.
   - Required fields: vin_or_serial, type, status.
   - Save and confirm record appears in list.
3) Applications
   - Go to Sales -> Applications -> Create.
   - Select Customer, set status = new, set requested_at.
   - Save and confirm list entry.
4) Application status transitions
   - Edit application and move: new -> reviewed -> approved -> converted.
   - Also validate reviewed -> rejected is allowed.
   - Attempt an invalid jump (new -> approved) and confirm rejection.
5) Orders
   - Create order from Sales -> Orders -> Create.
   - Required: Customer, order_number, status = draft, total_amount.
   - Optionally link to Application.
6) Order status transitions
   - Move draft -> confirmed -> completed.
   - Test cancel from draft/confirmed.
7) Reservations
   - Create reservation from Sales -> Reservations -> Create.
   - Required: Order, Vehicle, reserved_from, reserved_until, status = active.
   - Move active -> fulfilled/expired/cancelled.
8) Internal Transfers (view + create)
   - Go to Operations -> Internal Transfers -> Create.
   - Create draft record; move draft -> submitted -> acknowledged -> closed.
   - Validate submitted -> cancelled allowed.
9) Customer Returns (view + create)
   - Go to Operations -> Customer Returns -> Create.
   - Create draft record; move draft -> reported -> reviewed -> closed.
   - Validate reported -> cancelled allowed.
10) Invoices (read-only)
   - Go to Invoicing -> Invoices; ensure create/edit actions are hidden.
11) Pricing/Contracts (read-only via Customer)
   - Open a Customer; confirm Contracts and Pricing Profiles are visible but
     read-only for this role.

### Back Office Role (orders, reservations, invoicing)
1) Applications (read-only)
   - View list; ensure create/edit actions are hidden.
2) Orders
   - Create Order and confirm full CRUD; test status transitions.
3) Reservations
   - Create Reservation and confirm full CRUD; test status transitions.
4) Customers and Vehicles
   - Create/edit Customers and Vehicles.
5) Customer Contracts and Pricing Profiles (full)
   - In a Customer record, add Contracts and Pricing Profiles.
6) Invoices (full)
   - Create Invoice for an Order; test status draft -> issued.
7) Payments (read-only)
   - View Payments list; ensure create/edit actions are hidden.
8) Internal Transfers and Customer Returns (full)
   - Create and update; validate status transitions.

### Finance Role (invoicing + payments)
1) Orders and Customers (read-only)
   - Verify view-only access.
2) Invoices (full)
   - Create invoice; move draft -> issued -> partially_paid -> paid.
   - Validate cancel rules.
3) Payments (full)
   - Create payment with status pending.
   - Move pending -> completed, then completed -> reversed.
   - Try pending -> failed and confirm it is allowed.
4) Turnover Overview (view)
   - Apply filters by period; verify read-only.
5) Auto-invoice check
   - With an Order completed (created by another role), confirm that
     a draft invoice exists if remaining amount > 0.

### Turnover Role (reporting)
1) Turnover Overview
   - View list, apply period filters.
2) Export
   - If export is visible, run export and confirm download appears.
3) Invoices (view-only)
   - Verify list is readable but no create/edit.

### Admin Role (CRM full access)
1) Full CRUD on all CRM entities.
2) Validate internal transfers, returns, and document registry.
3) Validate exports in CRM modules.
4) Perform one complete end-to-end CRM flow.

### superadmin (CRM + HR full access)
1) Perform one complete CRM flow and one complete HR flow.
2) Verify HR scope checks are bypassed and all records are visible.

## HR Role Manuals and Test Steps

### hr_admin (full HR access)
1) Org Structure
   - Create Branches, Departments, Positions, Contract Types.
2) Employees
   - Create a User, then create Employee linked to that user.
   - Set manager_user_id, department, branch, position, contract type.
   - Test status transitions: active -> suspended -> active/left.
3) Employee Documents
   - Upload at least one document per employee.
4) KPI Templates and Cycles
   - Create KPI Template with items and weights.
   - Create KPI Cycle.
5) KPI Reports
   - Create KPI Report (employee + template + cycle).
   - Add report items and scores; confirm computed totals.
   - Move draft -> submitted -> manager_reviewed -> closed.
6) Training
   - Create Training Session (scheduled -> completed/cancelled).
   - Add Participants; test attendance and result statuses.
7) Recruitment
   - Create Candidate; move stage application -> interview -> offer -> hired.
8) Onboarding
   - Create Onboarding Template and tasks.
   - Create Employee Onboarding; move not_started -> in_progress -> completed.
   - Update task statuses pending/in_progress/blocked/completed.
9) Feedback
   - Create Feedback Cycle (draft -> open -> closed).
   - Create Feedback Requests; set rater and subject; submit answers.
10) Engagement Surveys
   - Create Survey, add questions, open -> close -> archive.
   - Create Survey Submissions and answers.

### hr_manager (full HR operations, scoped)
1) Validate scoping
   - Confirm only employees in your department/team are visible.
2) Employees and Documents
   - Full CRUD within scope; validate status transitions.
3) KPI Cycles/Reports
   - Create cycles/reports; update reports; validate transitions.
4) Training Sessions/Participants
   - Full CRUD within scope; validate attendance/result transitions.
5) Candidates and Onboarding
   - Full CRUD for candidates and employee onboarding within scope.
6) Feedback Requests and Surveys
   - Full CRUD within scope; validate feedback and survey transitions.
7) View-only areas
   - Org Structure, KPI Templates, Onboarding Templates, Feedback Cycles should
     be visible but not editable.

### department_manager (view + update limited)
1) Validate scoping
   - Only department/team records visible.
2) View-only modules
   - Org Structure, Employees, Documents, KPI Templates/Cycles, Training
     Sessions, Candidates, Onboarding Templates, Feedback Cycles, Engagement
     Surveys must be view-only.
3) Update-permitted modules
   - KPI Reports, Training Participants, Employee Onboardings, Feedback
     Requests, Survey Submissions.
   - Verify updates are allowed and status transitions enforced.

## Cross-Role and System Validations

### Status Transition Matrix (Spot Checks)
- Applications: new -> reviewed -> approved -> converted; reviewed -> rejected.
- Orders: draft -> confirmed -> completed | cancelled.
- Reservations: active -> fulfilled | expired | cancelled.
- Invoices: draft -> issued -> partially_paid | paid | cancelled; partially_paid -> paid.
- Payments: pending -> completed | failed; completed -> reversed.
- Internal Transfers: draft -> submitted -> acknowledged -> closed; submitted -> cancelled.
- Customer Returns: draft -> reported -> reviewed -> closed; reported -> cancelled.
- HR statuses (examples):
  - Employee: active -> suspended/left; suspended -> active/left.
  - KPI Report: draft -> submitted -> manager_reviewed -> closed.
  - Training Session: scheduled -> completed/cancelled.
  - Training Attendance: invited -> confirmed -> attended/no_show/cancelled.
  - Recruitment: application -> interview -> offer -> hired.
  - Onboarding: not_started -> in_progress -> completed; cancel from early states.
  - Onboarding Task: pending -> in_progress -> completed; pending/in_progress -> blocked.
  - Feedback Cycle: draft -> open -> closed.
  - Feedback Request: pending -> submitted/ cancelled; submitted -> closed.
  - Survey: draft -> open -> closed -> archived.

### Auto-Invoice Behavior (CRM)
- Completing an Order should create a draft invoice for remaining billable
  amount (total_amount - discount_amount - sum(invoices)).
- Auto-invoice is skipped if remaining <= 0 or fully paid.

### Document Registry
- Operations -> Document Registry shows Applications, Orders, Reservations,
  Invoices, and Payments in one list.
- Each row links to the original record.

### Audit Logs
- Status changes and deletions should generate audit log entries.

### Exports
- Exports appear only when role has export permission.
- Confirm export file and notifications appear after queue runs.

### Notifications (HR)
- Contract expiration reminders (daily schedule).
- Onboarding delay alerts (daily schedule).
- Survey open/close notifications (status-driven).

## Out of Scope for Manual Tests
- Procurement, inventory, warehouse, marketing, payroll, and finance
  reporting beyond Turnover Overview are not implemented in this codebase.

