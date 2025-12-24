# CRM Business Guide

## Purpose
This guide explains the CRM's real-world business flow and provides role-by-role operating instructions.

## Executive Summary
The CRM tracks the commercial flow from application to payment and turnover reporting. Detailed
process rules (status gates, automations, calculations) live in `docs/Business_Processes.md`.
Technical architecture and the data model live in `docs/CRM_Technical_Reference.md`.

## Where to Find Process Rules
- Status transitions, conversion rules, auto-invoicing logic, and amount handling:
  `docs/Business_Processes.md`.
- Permissions matrix and schema details: `docs/CRM_Technical_Reference.md`.

## Roles and Responsibilities (Business View)

### Sales
Primary role: intake, qualification, and conversion to orders.
- Create and update Customers.
- Create and update Vehicles (if asset inventory is maintained by Sales).
- Create Applications and move them through status transitions.
- Convert approved applications into Orders (via the "Convert to Order" action).
- Create and manage Reservations to allocate assets to orders.
- View Invoices (read-only).

### Back Office
Primary role: operational follow-through and billing setup.
- View Applications (read-only).
- Create and manage Orders.
- Create and manage Reservations.
- Create and manage Invoices (billing documents).
- View Payments (read-only).

### Finance
Primary role: invoicing completion, payment tracking, and cash recognition.
- View Orders and Customers (read-only).
- Create and manage Invoices and move them through status transitions.
- Create and manage Payments and track settlement.
- View Turnover Overview (monthly invoiced vs. paid).

### Turnover
Primary role: reporting.
- View Turnover Overview.
- Export turnover or invoice data if permitted.

### Admin
Full access to all modules and actions.

## Role Manuals
Full permission details live in `docs/CRM_Technical_Reference.md`.

---
### Sales Role Manual

#### Access
- Applications: full
- Orders: full
- Reservations: full
- Customers: full
- Vehicles: full
- Invoices: view-only

#### Step-by-Step Flow
1) Create Customer
   - Go to Clients & Assets -> Customers -> Create.
   - Fill required fields: first_name, last_name, personal_id_or_tax_id, phone.
   - Save.

2) Create Vehicle
   - Go to Clients & Assets -> Vehicles -> Create.
   - Fill: vin_or_serial, type, status (available/reserved/sold).
   - Save.

3) Create Application (Lead/Request)
   - Go to Sales -> Applications -> Create.
   - Select Customer, set status = new, set requested_at.
   - Save.

4) Advance Application Status
   - Edit application.
   - Move along the allowed status path.

5) Create Order
   - Go to Sales -> Orders -> Create.
   - Select Customer, optionally link Application.
   - Fill order_number, status = draft, total_amount.
   - Save.

6) Advance Order Status
   - Edit order.
   - Move along the allowed status path.

7) Create Reservation
   - Go to Sales -> Reservations -> Create.
   - Select Order and Vehicle.
   - Set reserved_from, reserved_until, status = active.
   - Save.

8) Advance Reservation Status
   - Edit reservation.
   - Move along the allowed status path.

9) View Invoices (Read-only)
   - Go to Invoicing -> Invoices.
   - View invoice details only.

---
### Back Office Role Manual

#### Access
- Applications: view-only
- Orders: full
- Reservations: full
- Customers: full
- Vehicles: full
- Invoices: full
- Payments: view-only

#### Step-by-Step Flow
1) View Applications
   - Sales -> Applications (read-only).

2) Manage Customers and Vehicles
   - Same steps as Sales.

3) Create and Manage Orders
   - Same steps as Sales.

4) Create and Manage Reservations
   - Same steps as Sales.

5) Create Invoice
   - Invoicing -> Invoices -> Create.
   - Select Order, fill invoice_number, status = draft, total_amount, issued_at.
   - Save.

6) Advance Invoice Status
   - Edit invoice.
   - Move along the allowed status path.

7) View Payments (Read-only)
   - Finance -> Payments.

---
### Finance Role Manual

#### Access
- Orders: view-only
- Customers: view-only
- Invoices: full
- Payments: full
- Turnover overview: view-only

#### Step-by-Step Flow
1) View Orders and Customers
   - Sales -> Orders (read-only).
   - Clients & Assets -> Customers (read-only).

2) Create and Manage Invoices
   - Invoicing -> Invoices -> Create/Edit.
   - Move along the allowed status path.

3) Create and Manage Payments
   - Finance -> Payments -> Create.
   - Select Invoice, fill amount, status = pending, payment_date, payment_method.
   - Save.

4) Advance Payment Status
   - Edit payment.
   - Move along the allowed status path.

5) View Turnover Overview
   - Turnover -> Turnover Overview (read-only).

---
### Turnover Role Manual

#### Access
- Turnover overview: full
- Invoices: view-only

#### Step-by-Step Flow
1) View Turnover Overview
   - Turnover -> Turnover Overview.
   - Filter by period as needed.

2) Export Turnover Overview (if permitted)
   - Click Export, select columns, confirm.
   - Download link appears in notifications.

3) View Invoices (Read-only)
   - Invoicing -> Invoices.

---
### Admin Role Manual

#### Access
- Full access to all modules.

#### Step-by-Step Flow
- Perform any of the Sales, Back Office, Finance, or Turnover flows.

## Hand-Offs and Accountability
- Sales to Back Office: application approval and order creation.
- Back Office to Finance: invoices prepared and issued.
- Finance to Leadership: turnover and payment reporting.

## Reporting and KPIs (Leadership View)
- Application conversion rate (new -> approved -> converted).
- Pipeline value (sum of orders by status).
- Asset utilization (reservations by status).
- Invoiced vs. paid totals per month (Turnover Overview).
- Outstanding receivables (invoices not paid).

## Practical Example (Real Life)
1) Sales logs an application for a new customer request.
2) After review, Sales approves it and converts it to an order.
3) Back Office confirms the order and reserves a vehicle.
4) After delivery, the order is marked completed.
5) The system creates a draft invoice automatically.
6) Finance finalizes and issues the invoice.
7) Payments are recorded as they arrive.
8) Leadership sees monthly invoiced vs. paid totals in Turnover Overview.

## Open Business Decisions (Potential Future Enhancements)
- Confirm whether auto-invoicing should occur on confirmed vs completed.
- Decide if due dates should be set automatically (e.g., +30 days).
- Decide whether draft invoices should allow issued_at = null.
- Decide on a fixed invoice numbering sequence for production.
- Decide if auto-invoice should block order completion on edge cases.
