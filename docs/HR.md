# HR Module Overview

This module extends the CRM with a dedicated HR bounded context. HR models reference
`users.id` only. CRM models do not reference HR.

## Scope
- Employee Management: profiles tied 1:1 to users, org structure, contracts, and documents.
- KPI & Performance: templates, cycles, reports, and weighted score calculations.
- Training & Development: sessions, participants, and results.
- Recruitment: candidate pipeline tracking.
- Onboarding: templates, employee onboarding, and task progress.
- 360 Feedback: cycles, requests, answers, and aggregation inputs.
- Engagement Surveys: surveys, questions, submissions, and answers.

## Roles & Permissions (HR)
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

Policy-level scoping restricts hr_manager and department_manager access to their
department/team records. Superadmin bypasses all scope checks.

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

## Filament Navigation
All HR resources are grouped under the "HR" navigation group in the admin panel,
ordered as:
1) Employees
2) Org Structure (Departments, Positions, Branches, Contract Types)
3) Documents
4) Performance (KPI Templates, Cycles, Reports)
5) Training (Sessions, Participants)
6) Recruitment (Candidates)
7) Onboarding (Templates, Employee Onboarding)
8) Feedback (Cycles, Requests)
9) Surveys (Engagement Surveys, Survey Submissions)

HR roles land on an HR-specific dashboard (CRM dashboard hidden), while superadmin
continues to see the standard CRM dashboard.

## Notifications
- Contract expiration reminders: scheduled job, sent to HR roles.
- Onboarding delay alerts: scheduled job, sent to HR roles and assigned managers.
- Survey open/close notifications: sent to active employees when status changes.
