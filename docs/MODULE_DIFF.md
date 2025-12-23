# Module Structure Alignment

This note summarizes how CRM and HR modules are structured and where they still differ.

## Where They Match
- Models grouped by subdomain under `app/Models/Crm/*` and `app/Models/Hr/*`.
- Enums grouped by domain under `app/Enums/Crm` and `app/Enums/Hr`.
- Observers and policies scoped by domain under `app/Observers/*` and `app/Policies/*`.
- Filament resources, pages, widgets, and exports under `app/Filament/*`.
- Optional layers exist for both domains: `app/Services/*`, `app/Jobs/*`, `app/Notifications/*`.

## Remaining Differences (Business-Driven)
- HR has more subdomains (Kpi/Onboarding/Recruitment/Training/Feedback/Survey) than CRM.
- CRM exports are implemented; HR exports are currently empty.
