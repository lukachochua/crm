# Project Technical Reference (Requirements Mapping)

This document maps the provided requirements list to the current codebase and notes what is implemented, partially implemented, or missing. It also summarizes the modules that exist today and how they map to the requirements.

## Status Legend
- Implemented: Available in code and UI.
- Partial: Some data or logic exists, but UI/reporting or full workflow is missing.
- Not implemented: No matching module or workflow in the codebase.
- External: Mentioned as coming from an external system (for example, finance reports), not implemented here.

## Modules in This Codebase
- CRM core: customers, applications, orders, reservations, invoices, payments, turnover overview.
- HR: employees, org structure, onboarding, training, KPI, recruitment, feedback, surveys.
- Exports: CRM exports via Filament.
- Admin routing: role-based landing (`/admin` -> `/admin/crm` or `/admin/hr`).

## Module-Level Status Summary

| Module | Status | Notes |
| --- | --- | --- |
| CRM Core (Customers, Applications, Orders, Reservations, Invoices, Payments) | Implemented | Core CRM flow exists in Filament resources and models. |
| Turnover Overview | Implemented | Read-only view based on invoices and payments. |
| HR Core (Employees, Org Structure, Onboarding, Training, KPI, Recruitment, Feedback, Surveys) | Implemented (with partial reporting) | Core workflows exist; some analytics/reporting and aggregation are partial. |
| Sales Ops (non-CRM operational docs) | Partial/Not implemented | Only overlaps with core CRM entities. |
| Procurement | Not implemented | No suppliers, goods, inventory, or procurement workflows. |
| Marketing | Not implemented | No campaigns, promotions, or marketing analytics. |
| General/Finance Ops | Partial/Not implemented | Some turnover info exists; most finance/warehouse reporting is absent. |

## Requirements Mapping (Full List)

### HR Requirements

| ID | Requirement | Status | Evidence/Notes |
| --- | --- | --- | --- |
| 1.1 | Unified employee profile: name, position, department, branch, contract type, start date, status (active/suspended/left) | Implemented | app/Models/Hr/Employee.php, app/Filament/Resources/Hr/EmployeeResource.php |
| 1.2 | Documents attached to profile (contract, applications, evaluation forms) | Implemented | app/Models/Hr/EmployeeDocument.php, app/Filament/Resources/Hr/EmployeeDocumentResource.php |
| 1.3 | Notification before contract expiry | Implemented | app/Jobs/Hr/SendContractExpirationReminders.php, app/Notifications/Hr/ContractExpirationReminder.php |
| 2.1 | KPI template linked to each position | Implemented | app/Models/Hr/Kpi/KpiTemplate.php, app/Filament/Resources/Hr/KpiTemplateResource.php |
| 2.2 | Employee self input + manager evaluation | Implemented | app/Models/Hr/Kpi/KpiReport.php, app/Filament/Resources/Hr/KpiReportResource.php |
| 2.3 | Automatic score/status calculation (performance %) | Implemented | app/Services/Hr/KpiScoreCalculator.php, app/Observers/Hr/KpiReportItemObserver.php |
| 2.4 | Reporting by department, month, quarter | Partial | app/Services/Hr/HrReportService.php (service exists; no UI reports) |
| 3.1 | Training calendar (date, topic, participants, status) | Implemented | app/Models/Hr/Training/TrainingSession.php, app/Filament/Resources/Hr/TrainingSessionResource.php |
| 3.2 | Per-employee training history and results | Implemented | app/Models/Hr/Training/TrainingParticipant.php, app/Filament/Resources/Hr/TrainingParticipantResource.php |
| 4.1 | Candidate database by position | Implemented | app/Models/Hr/Recruitment/Candidate.php, app/Filament/Resources/Hr/CandidateResource.php |
| 4.2 | Stage control (application/interview/offer/hired) | Implemented | app/Enums/Hr/RecruitmentStage.php, app/Models/Hr/Recruitment/Candidate.php |
| 4.3 | Status monitoring and analytics (positions closed, candidates progressed, etc.) | Partial | app/Filament/Widgets/Hr/RecruitmentPipelineChart.php (stage counts only) |
| 5.1 | Onboarding tasks list with stage, remaining, and overdue visibility | Implemented | app/Models/Hr/Onboarding/EmployeeOnboarding.php, app/Models/Hr/Onboarding/EmployeeOnboardingTask.php, app/Filament/Widgets/Hr/OverdueOnboardingWidget.php |
| 5.2 | Notifications about onboarding changes | Partial | app/Jobs/Hr/SendOnboardingDelayAlerts.php (overdue only) |
| 6.1 | 360 feedback (manager/peer/self) via CRM forms | Implemented | app/Models/Hr/Feedback/FeedbackCycle.php, FeedbackRequest.php, FeedbackAnswer.php, app/Filament/Resources/Hr/FeedbackCycleResource.php, FeedbackRequestResource.php |
| 6.2 | Feedback results stored on employee profile and shown in evaluation field | Not implemented | app/Models/Hr/Employee.php has fields but no aggregation or UI exposure. |
| 7.1 | Engagement survey module with automatic reporting | Partial | app/Models/Hr/Survey/* and app/Filament/Resources/Hr/EngagementSurveyResource.php exist; no analytics reporting. |

#### HR - Additional Notes from Excel

| Requirement | Status | Notes |
| --- | --- | --- |
| Active employees by branch | Partial | Data exists (employee -> branch + status); no reporting UI. Excel note: "from finance reference data". |
| Left employees by branch | Partial | Data exists (employee -> branch + status); no reporting UI. Excel note: "from finance reference data". |

### Sales Operations (Process/Operations)

Excel columns (where present): Invoices, Back Office, Finance, Turnover. Values include "invoice view", "yes", and "full". These are preserved below as access hints.

| Requirement | Status | Excel Access Hints | Evidence/Notes |
| --- | --- | --- | --- |
| Customer database (clients, vehicles, contracts, price statuses, ...) | Partial | Invoices: view; Back Office: yes; Finance: yes; Turnover: full | Customers/Vehicles exist; no contracts/pricing status module. |
| Active applications | Implemented | Invoices: view; Back Office: yes; Finance: yes; Turnover: full | CRM Applications. |
| Customer orders (parts reservation) | Partial | Invoices: view; Back Office: yes; Finance: yes; Turnover: full | Orders/Reservations exist; no parts inventory/reservation workflow. |
| Customer returns | Not implemented | Invoices: view; Finance: yes; Turnover: full | No returns module. |
| Internal transfers | Not implemented | Invoices: view; Back Office: yes; Finance: yes; Turnover: full | No internal transfer module. |
| Created documents: outgoing application | Partial | Invoices: view; Back Office: yes; Finance: yes; Turnover: full | Applications exist; no dedicated outbound doc type. |
| Customer payments | Implemented | Invoices: view | CRM Payments exist. |
| All documents visible (unified) | Not implemented | Invoices: view; Back Office: yes; Finance: yes; Turnover: full | No unified document registry. |
| Internal transfer (document) | Not implemented | Invoices: view; Finance: yes; Turnover: full | No internal transfer module. |
| Customer return (document) | Not implemented | Invoices: view; Finance: yes; Turnover: full | No customer returns module. |

### Procurement (Process/Operations)

All items below are not implemented in this codebase. Excel access hints are preserved.

| Requirement | Status | Excel Access Hints | Notes |
| --- | --- | --- | --- |
| Goods procurement (with suppliers) | Not implemented | Finance: yes; Turnover: full | No supplier/procurement modules. |
| Goods groups | Not implemented | Finance: yes; Turnover: full | No inventory groups. |
| Goods | Not implemented | Finance: yes; Turnover: full | No goods inventory. |
| Goods consumed by service | Not implemented | Finance: yes; Turnover: full | No service inventory tracking. |
| Tools taken for service | Not implemented | Finance: yes; Turnover: full | No tool intake tracking. |
| Fixed asset groups | Not implemented | Finance: yes; Turnover: full | No fixed assets module. |
| Tools | Not implemented | Finance: yes; Turnover: full | No tools module. |
| Equipment | Not implemented | Finance: yes; Turnover: full | No equipment module. |
| Payments | Not implemented | Finance: yes; Turnover: full | Procurement payments not implemented. |
| Credit notes | Not implemented | Finance: yes; Turnover: full | No credit note module. |
| Supplier yearly analysis (turnover, credit notes, bonuses, scrap bonuses) | Not implemented | Finance: yes; Turnover: full | No supplier analytics. |
| Unspecified procurement row (empty in source) | Not implemented | Finance: yes; Turnover: full | Source row had no label. |
| Full goods history with all fields | Not implemented | Finance: yes | No inventory history. |

### Marketing

| Requirement | Status | Notes |
| --- | --- | --- |
| Sales turnover | Partial | TurnoverOverview exists but only invoiced/paid totals. |
| Client turnover shown fully | Not implemented | No client-level turnover report. |
| Goods register (inventory ledger) | Not implemented | Excel note: finance report. |
| Service operations | Not implemented | Excel note: finance report (custom operation built by team). |
| Payments | Partial | CRM payments exist, no marketing view. |
| Goods turnover by client (oil/tires/parts/service) | Not implemented | Excel note: finance has a corporate sales report. |
| Promotions visibility (when enabled, results, buyer) | Not implemented | No promotions module. |
| Promotion source attribution (which campaign brought client) | Not implemented | No attribution module. |
| Email/SMS offers | Not implemented | No campaign messaging. |
| Goods analysis by month/brand/top sellers/slow movers | Not implemented | Excel note: report. |

### Clarifications Needed

These are not implemented and need clarification before scoping:
- Call center requirements
- Customer survey requirements
- Recruitment technical setup specifics
- Performance evaluation specifics beyond current KPI/feedback

### General Requirements

| Requirement | Status | Notes |
| --- | --- | --- |
| Warehouse turnover by warehouse, by month, with margin breakdown | Not implemented | Excel note: finance report. |
| Warehouse stock balances by warehouse | Not implemented | No inventory/warehouse module. |
| Payroll calculation by bonus scheme per branch/department | Not implemented | No payroll module. |
| Accounts receivable (debtors) | Partial | TurnoverOverview exists but no AR ledger. Excel note: finance operation. |
| Corporate sales report with breakdown by parts/oil/tires/service and YoY deltas | Not implemented | Excel note: finance operation. |
| Offer tracking with full case info and attachments | Not implemented | No offers/cases module. |
| Client inquiry registration by corporate manager (Excel upload + doc storage) | Not implemented | No inquiry intake module. |
| Supplier email ingestion (Excel/PDF) into system | Not implemented | No email ingestion workflow. |
| Goods turnover by warehouse | Not implemented | Excel note: finance operation. |
| Total goods turnover | Not implemented | Excel note: finance operation. |

## Notes on Evidence Sources
- CRM technical details: `docs/CRM_Technical_Reference.md`.
- HR technical details: `docs/HR_Technical_Reference.md`.
- Compliance mapping: `docs/CRM_Compliance_Report.md`.
