# Spec: Sales Order Discounts & Strategic Targets

## Overview
Enhance the Sales Order workflow with strategic commitment tracking and sophisticated discount management. This introduces a "Commitment vs. Consumption" model where Sales Representatives (SRs) can propose specific, time-bound targets that are automatically fulfilled by their sales activity.

## 1. Real-Life Scenario: The "Commitment vs. Consumption" Flow

### 1.1 The Commitment (Target Setting)
In January, a Sales Representative (e.g., Budi) identifies a strategic opportunity for **Product A** from **Principal X**.
- **Goal**: Proposes a commitment to sell **1,000 units** of Product A by June 30th.
- **Financials**: Estimates $100,000 in revenue with an $8,000 profit.
- **Persistence**: The system saves this as a "Strategic Target" (initially self-proposed/auto-approved).

### 1.2 The Transaction (Sales Order)
In March, Budi enters a Sales Order for **Permata Hospital** for **200 units** of Product A.
- **Negotiation**: Budi applies a **5% discount** to close the deal.
- **Interaction**: The system identifies that this order aligns with Budi's active Strategic Target.

### 1.3 The "Target Section" (Real-Time Feedback)
While creating the order, a dedicated UI section provides immediate feedback:
- **Visual Progress**: Shows the SR's target for Product A (e.g., 1,000 units | Already Sold: 150 | Remaining: 850).
- **Potential Impact**: As Budi enters "200 units", the UI predicts the new state (Remaining: 650 units).
- **Target Reduction**: Upon order confirmation, the "Remaining" balance is formally reduced.

## 2. Technical Design

### 2.1 Strategic Target Model (`strategic_targets`)
A new model to track specific, ad-hoc commitments alongside standard annual quotas.

| Field | Type | Description |
| :--- | :--- | :--- |
| `user_id` | foreignId | The SR making the commitment. |
| `targetable_type` | string | Polymorphic: Product, Principal, or Project. |
| `targetable_id` | unsignedBigInt | ID of the targetable entity. |
| `quantity` | integer | Target number of units. |
| `target_value` | decimal(16,2) | Total estimated revenue. |
| `estimated_profit` | decimal(16,2) | Calculated/Estimated profit. |
| `start_date` | date | Start of the commitment period. |
| `end_date` | date | Expiration date of the commitment. |
| `status` | string | `proposed`, `approved`, `rejected` (default: `approved`). |
| `metadata` | json | For additional context or audit logs. |

### 2.2 Consumption Logic
- **Trigger**: When a Sales Order reaches `confirmed` or `delivered` status.
- **Matching**: The system searches for active `StrategicTargets` for the SR and the products within the order.
- **Update**: Increments `actual_quantity` and `actual_value` on the target (these fields will be added to the model).

### 2.3 Discount Management
- **Order Level**: Enhancement to `orders` table to track discount justifications.
- **Item Level**: Refinement of `order_items` discount application to ensure it subtracts from the `subtotal` before calculating `net_sales_total`.

## 3. UI/UX Requirements (Filament)

### 3.1 Target Section in Sales Order Form
- **Dynamic Table**: Lists active targets relevant to the items being added to the current order.
- **Live Calculations**: A reactive footer or side-panel showing:
    - Current Target Progress.
    - Post-Order Target Progress.
    - Potential Target Reduction.

### 3.2 Target Proposal Management
- New Resource in Filament to allow SRs to create and manage their own Strategic Targets.
- View to see "Annual Target" vs. "Self-Proposed Targets" in one unified dashboard.

## 4. Proposed Database Changes

### New Table: `strategic_targets`
- `id`: primary key
- `user_id`: foreignId
- `targetable_id`: unsignedBigInt
- `targetable_type`: string
- `target_quantity`: integer
- `actual_quantity`: integer (default 0)
- `target_revenue`: decimal(16,2)
- `actual_revenue`: decimal(16,2) (default 0)
- `estimated_cost`: decimal(16,2)
- `estimated_profit`: decimal(16,2)
- `start_date`: date
- `end_date`: date
- `status`: string (default 'approved')
- `timestamps`

### Changes to `orders`
- `target_id`: foreignId (optional link to a specific strategic target for tracking)
