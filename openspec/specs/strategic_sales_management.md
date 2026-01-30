# Spec: Strategic Sales Management & Confidence Scoring

## Overview
Transform the CRM from a transactional logging system into a target-driven strategic tool. This specification introduces objective sales forecasting via a milestone-based Confidence Checklist and performance monitoring through explicit Sales Targets.

## 1. Confidence Level Scoring System
The existing `confidence_level` (integer) on `Visits` and `Leads` will be calculated based on a standardized checklist.

### 1.1 Checklist Items (The "Objective Proof")
| Item | Weight | Description |
| :--- | :--- | :--- |
| **Budget Confirmed** | 20% | Customer has allocated funds for the purchase. |
| **Decision Maker Met** | 15% | Direct contact with the person who signs the PO. |
| **Need Validated** | 20% | Clinical/Technical problem identified and solution agreed. |
| **Timeline Established** | 15% | Targeted procurement quarter/month is fixed. |
| **Technical Compliance** | 20% | Product specs meet all local/tender requirements. |
| **Trial/Demo Success** | 10% | Physical demonstration completed and approved. |

### 1.2 Implementation
- **Master Data**: `milestones` table to store the standard checklist items and their weights.
- **Pivot Data**: `lead_milestone` table to track the completion status for each lead.
- **Logic**: An observer that calculates the `leads.confidence_level` by summing the weights of completed milestones.

...

## 4. Proposed Database Changes

### `milestones` table (Master Data)
- `name`: string (e.g., "Budget Confirmed")
- `weight`: integer (0-100)
- `description`: text (nullable)
- `is_active`: boolean (default: true)

### `lead_milestone` table (Pivot)
- `lead_id`: foreignId (cascade on delete)
- `milestone_id`: foreignId (restrict on delete)
- `is_completed`: boolean (default: false)
- `completed_at`: timestamp (nullable)
- `notes`: text (nullable)

### `leads` table
- `confidence_level`: integer (default: 0) - *Now a calculated field updated via events*
- `expected_closing_date`: date (nullable)
- `financial_goal`: decimal (optional, for comparing vs estimate)

### `users` table
- `sales_target`: decimal (default: 0)
- `target_metadata`: json (for monthly splits)

### `principals` table
- `annual_target`: decimal (default: 0)
- `supplier_type`: enum ['IVD', 'CL', 'Non-CL'] (from images)
- `website`: string (nullable)

### `visits` table
- `next_contact_date`: date (nullable)
- `follow_up_notes`: text (nullable)

## 5. UI/UX (Filament)
- **Checklist Widget**: A set of toggles on the Lead Edit page.
- **Progress Bar**: Visual representation of Confidence Level based on the checklist.
- **Target vs Actual**: New widget on User Dashboard showing a gauge of "Current Revenue vs Target."
