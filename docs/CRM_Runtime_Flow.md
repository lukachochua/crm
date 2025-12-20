# CRM Runtime Flow Diagram

This document explains how a request flows through the CRM at runtime and how access control is enforced.

## High-Level Runtime Flow (Text Diagram)
```
Browser (Filament UI)
    |
    v
Filament Page/Action
    |
    v
Policy Check (Gate::allows / Model Policy)
    |
    +--> Deny -> 403 / Action hidden
    |
    v
Eloquent Model (create/update/delete)
    |
    v
Status Guard (EnforcesStatusTransitions)
    |
    +--> Invalid transition -> Validation error
    |
    v
DB Write (MySQL/Postgres)
    |
    v
Observers (status/payment/deletion)
    |
    v
Audit Logger -> audit_logs table
```

## Access Control Flow (User -> Role -> Permission)
```
User (App\Models\User, HasRoles)
    |
    v
Role(s) and Permissions (Spatie tables)
    |
    v
Policy methods call $user->can('entity.action')
    |
    v
Filament checks canViewAny / canCreate / canEdit / canDelete
```

## Export Flow (When Export is Clicked)
```
ExportAction (Filament)
    |
    v
Exporter class (app/Filament/Exports/*Exporter.php)
    |
    v
exports table row created
    |
    v
Queue job runs (or sync)
    |
    v
File stored on disk + notification created
    |
    v
User downloads via /admin/exports/{id}/download
```

## Notes
- Filament hides navigation and actions when policy checks fail.
- Status transitions are enforced at the model layer (not just UI).
- Audit logs are written by observers and are not editable in the UI.
