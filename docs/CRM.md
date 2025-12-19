# CRM System Overview

## Architecture overview
- Laravel 12 + Filament v3 internal CRM
- Spatie Laravel Permission + Laravel Policies for all access control
- Normalized relational schema with soft deletes for business entities
- Enum-based status fields with strict transition validation
- Audit logs for status changes, financial actions, and deletions
- Turnover overview derived from invoices and payments (read-only view)
- Audit logs are immutable and never deleted

## Role and permission matrix
Legend:
- view = read-only
- full = view + create + update + delete + export
- none = no access

| Capability | Sales | Back Office | Finance | Turnover | Admin |
| --- | --- | --- | --- | --- | --- |
| Applications / Requests | full | view | none | none | full |
| Orders | full | full | view | none | full |
| Reservations | full | full | none | none | full |
| Customers database | full | full | view | none | full |
| Vehicles / Assets | full | full | none | none | full |
| Invoices | view | full | full | view | full |
| Payments | none | view | full | none | full |
| Turnover overview | none | none | view | full | full |

## Database schema
### applications
- id, customer_id, status, requested_at, created_by, description, source, internal_notes, timestamps, deleted_at

### orders
- id, customer_id, application_id, order_number, status, total_amount, discount_amount, notes, created_by, timestamps, deleted_at

### reservations
- id, order_id, vehicle_id, status, reserved_from, reserved_until, notes, timestamps, deleted_at

### customers
- id, first_name, last_name, personal_id_or_tax_id, phone, email, address, notes, timestamps, deleted_at

### vehicles
- id, vin_or_serial, type, status, model, year, color, notes, timestamps, deleted_at

### invoices
- id, order_id, invoice_number, status, total_amount, issued_at, due_date, notes, timestamps, deleted_at

### payments
- id, invoice_id, amount, status, payment_date, created_by, payment_method, reference_number, notes, timestamps, deleted_at

### audit_logs
- id, auditable_type, auditable_id, action_type, performed_by, performed_at, before_state, after_state, amount_before, amount_after, currency, notes, ip_address

### turnover_overviews (view)
- period (YYYY-MM), total_invoiced, total_paid, outstanding_amount

## Enums and transitions
- application_status: new -> reviewed -> approved -> converted; reviewed -> rejected
- order_status: draft -> confirmed -> completed | cancelled
- reservation_status: active -> fulfilled | expired | cancelled
- invoice_status: draft -> issued -> partially_paid | paid | cancelled; partially_paid -> paid
- payment_status: pending -> completed | failed; completed -> reversed

## Setup instructions
1. Configure `.env` for MySQL (defaults already set to `crm`)
2. Install dependencies: `composer install`
3. Run migrations: `php artisan migrate`
4. Seed roles and permissions: `php artisan db:seed`
5. Create a Filament admin user: `php artisan make:filament-user`
6. Access Filament at `/admin`
