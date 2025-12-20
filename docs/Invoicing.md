# Invoicing Process (Current Behavior)

This document describes how invoicing works in the CRM as implemented today.

## Concepts and Relationships
- Order: the commercial commitment. It can have many invoices.
- Invoice: a billing record linked to one order.
- Payment: a money movement linked to one invoice.
- Turnover overview: a monthly aggregation of invoice totals vs. payments.

## Invoice Lifecycle and Status
Invoice statuses (with allowed transitions):
- draft -> issued -> partially_paid -> paid
- issued -> cancelled

Status transitions are enforced by model rules, so invalid jumps are blocked.

## Manual Invoicing (UI Flow)
Invoices can always be created and managed manually:
1) Go to Invoicing -> Invoices -> Create.
2) Select the Order, then fill invoice_number, status, total_amount, issued_at.
3) Save and update status as payment progresses.

Multiple invoices per order are allowed (partial billing is supported).

## Auto-Invoice on Order Completion
When an order status changes to `completed`, the system attempts to auto-create
a draft invoice with defaults:
- status: draft
- issued_at: now()
- due_date: null
- notes: "Auto-created when order completed."
- invoice_number: "INV-YYYYMMDD-####" (randomized per day, uniqueness checked)

### How the Amount Is Calculated
Billable total is defined as:
`billable_total = max(0, order.total_amount - order.discount_amount)`

If there are existing invoices, the auto-invoice uses the remaining amount:
`remaining = billable_total - sum(existing invoices)`

If remaining <= 0, auto-invoice is skipped.

### When Auto-Invoice Is Skipped
Auto-invoice will not create a new invoice if:
- there is no remaining amount to invoice, OR
- the order already has invoices and it is fully paid.

"Fully paid" is computed as:
`sum(completed payments) - sum(reversed payments) >= billable_total`

When auto-invoice is skipped in the web UI, a warning notification is shown.
In console contexts, notifications are suppressed.

## Payments and Settlement
Payments are linked to invoices and have statuses:
- pending -> completed -> reversed
- pending -> failed

Only completed and reversed payments affect the "fully paid" calculation.
Pending or failed payments do not count toward settlement.

## Turnover Overview
Turnover is computed by a database view that sums:
- total invoiced (from invoices)
- total paid (from payments)
and shows the outstanding amount for each month.

## Audit Logging
Audit logs are recorded when:
- invoice status changes
- invoice total changes
- payment is created or changes status
- order/application/reservation status changes
- records are deleted

Some audit events require an authenticated user. The auto-invoice path does not
create audit entries on its own, but later invoice changes will be logged.

## Current Assumptions and Defaults
- Order `total_amount` is the pre-discount price.
- Order `discount_amount` is an absolute amount (not a percentage).
- Discounts apply once at the order level, not per invoice.
- Multiple invoices per order are allowed; auto-invoice uses remaining balance.

## Known Limitations / Future Decisions
- Whether auto-invoicing should trigger on `confirmed` vs `completed`.
- Whether draft invoices should allow `issued_at = null`.
- Whether due dates should be set automatically (e.g., +30 days).
- The definitive invoice number format/sequence for production.
- Whether auto-invoice should block order completion or only warn/skip.
