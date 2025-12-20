# CRM Business Process (CEO Overview)

This document explains how the CRM works in real life, end-to-end, and how each
role uses it day-to-day. It is written for a non-technical business audience
but stays accurate to the current system behavior.

## Executive Summary
The CRM tracks the full commercial flow:
1) **Customer inquiry (Application)**
2) **Commercial commitment (Order)**
3) **Asset allocation (Reservation)**
4) **Billing (Invoice)**
5) **Cash collection (Payment)**
6) **Financial reporting (Turnover Overview)**

Each step is represented by a record with a defined status. Roles have specific
permissions to create, update, and export data. Audit logs record key changes.

## End-to-End Flow (Real Life)
1) A customer submits a request. Sales creates an **Application**.
2) Sales reviews and qualifies the request, moving the application through
   statuses (new -> reviewed -> approved).
3) Sales converts the approved application into an **Order** (manual action).
4) The order is confirmed and delivered. When an order becomes **Completed**,
   the system auto-creates a **draft Invoice** for the remaining billable amount.
5) Back Office or Finance finalizes the invoice (status progression to issued).
6) Finance records **Payments** against invoices as money arrives.
7) The **Turnover Overview** aggregates monthly invoiced vs. paid totals.

This is a flexible flow: orders can exist without applications, invoices can be
created manually, and multiple invoices can be issued for one order.

## Roles and Responsibilities (Business View)

### Sales
Primary role: intake, qualification, and conversion to orders.
- Create and update **Customers**.
- Create and update **Vehicles** (if asset inventory is maintained by Sales).
- Create **Applications** and move them through status transitions.
- Convert approved applications into **Orders** (via the “Convert to Order” action).
- Create and manage **Reservations** to allocate assets to orders.
- View **Invoices** (read-only).

### Back Office
Primary role: operational follow-through and billing setup.
- View Applications (read-only).
- Create and manage **Orders**.
- Create and manage **Reservations**.
- Create and manage **Invoices** (billing documents).
- View **Payments** (read-only).

### Finance
Primary role: invoicing completion, payment tracking, and cash recognition.
- View Orders and Customers (read-only).
- Create and manage **Invoices** and move them through status transitions.
- Create and manage **Payments** and track settlement.
- View **Turnover Overview** (monthly invoiced vs. paid).

### Turnover
Primary role: reporting.
- View **Turnover Overview**.
- Export turnover or invoice data if permitted.

### Admin
Full access to all modules and actions.

## Business Objects and Statuses

### Application (customer request)
Statuses:
- new -> reviewed -> approved -> converted
- reviewed -> rejected

Business meaning:
- **new**: intake logged.
- **reviewed**: qualified by Sales.
- **approved**: go-ahead granted.
- **converted**: an order has been created.
- **rejected**: not pursued.

### Order (commercial commitment)
Statuses:
- draft -> confirmed -> completed
- draft -> confirmed -> cancelled

Business meaning:
- **draft**: prepared but not committed.
- **confirmed**: accepted by business and customer.
- **completed**: work/delivery finished.
- **cancelled**: closed without fulfillment.

### Reservation (asset allocation)
Statuses:
- active -> fulfilled OR expired OR cancelled

Business meaning:
- **active**: asset held for the order.
- **fulfilled**: asset delivered/used.
- **expired**: reservation lapsed.
- **cancelled**: reservation voided.

### Invoice (billing document)
Statuses:
- draft -> issued -> partially_paid -> paid
- issued -> cancelled

Business meaning:
- **draft**: internal preparation.
- **issued**: formally sent/recognized for billing.
- **partially_paid**: some payment received.
- **paid**: fully settled.
- **cancelled**: voided bill.

### Payment (cash movement)
Statuses:
- pending -> completed -> reversed
- pending -> failed

Business meaning:
- **pending**: initiated but not settled.
- **completed**: received.
- **reversed**: refunded or chargeback.
- **failed**: did not settle.

## Invoicing Logic (Current Behavior)
Invoices can be created manually or automatically:
- Manual: Finance/Back Office creates an invoice anytime.
- Automatic: when an order status changes to **completed**, the system creates
  a **draft invoice** for the remaining billable amount.

Billable total is defined as:
- `order.total_amount - order.discount_amount` (never below zero).

If invoices already exist, auto-invoicing uses the remaining amount:
- `billable_total - sum(existing invoices)`.

If the order is already fully paid and invoiced, auto-invoice is skipped and a
warning is shown in the UI.

## Hand-Offs and Accountability
- **Sales to Back Office**: application approval and order creation.
- **Back Office to Finance**: invoices prepared and issued.
- **Finance to Leadership**: turnover and payment reporting.

## Reporting and KPIs (What Leadership Can Track)
From the system data, leadership can track:
- Application conversion rate (new -> approved -> converted).
- Pipeline value (sum of orders by status).
- Asset utilization (reservations by status).
- Invoiced vs. paid totals per month (Turnover Overview).
- Outstanding receivables (invoices not paid).

## Audit and Compliance
The CRM logs:
- status changes on applications, orders, reservations, invoices, payments
- invoice total changes
- payment creation events
- record deletions

These logs help with internal control and compliance.

## Practical Example (Real Life)
1) Sales logs an application for a new customer request.
2) After review, Sales approves it and converts it to an order.
3) Back Office confirms the order and reserves a vehicle.
4) After delivery, the order is marked completed.
5) The system creates a draft invoice automatically.
6) Finance finalizes and issues the invoice.
7) Payments are recorded as they arrive.
8) CEO sees monthly invoiced vs. paid totals in Turnover Overview.

## Current Flexibilities (Intentional)
- Orders can exist without an application (direct sales).
- Multiple invoices per order (partial billing).
- Manual overrides for invoice totals and dates.

These enable real-world exceptions while keeping a structured audit trail.

## Open Business Decisions (Potential Future Enhancements)
- Confirm whether auto-invoicing should occur on **confirmed** vs **completed**.
- Decide if due dates should be set automatically (e.g., +30 days).
- Decide on a fixed invoice numbering sequence for production.
- Decide if auto-invoice should block order completion on edge cases.
