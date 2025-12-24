# HR Module Overview

This module extends the CRM with a dedicated HR bounded context. HR models reference
`users.id` only. CRM models do not reference HR.

## Scope
- Employee Management: profiles tied 1:1 to users, org structure, contracts, and documents.
- KPI and Performance: templates, cycles, reports, and weighted score calculations.
- Training and Development: sessions, participants, and results.
- Recruitment: candidate pipeline tracking.
- Onboarding: templates, employee onboarding, and task progress.
- 360 Feedback: cycles, requests, answers, and aggregation inputs.
- Engagement Surveys: surveys, questions, submissions, and answers.

## Navigation Groups
For HR-only users, resources are grouped by function:
- People (employees, documents, contract types)
- Org Structure (branches, departments, positions)
- Recruitment (candidates)
- Onboarding (templates, employee onboarding)
- Performance (KPI templates, cycles, reports)
- Training (sessions, participants)
- Feedback (cycles, requests)
- Surveys (engagement surveys, submissions)

For users with non-HR roles, HR items collapse into a single `HR` group.

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

Policy-level scoping restricts hr_manager and department_manager access to their
department/team records. Superadmin bypasses all scope checks.

## Operational Guidance
- Business process rules (status flows, automations, notifications):
  `docs/Business_Processes.md`.
- Technical architecture, data model, and scheduling details:
  `docs/HR_Technical_Reference.md`.
