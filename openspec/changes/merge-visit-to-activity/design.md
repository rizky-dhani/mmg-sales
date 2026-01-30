# Design: Merge Visit into Activity

## Architecture

### 1. Database Schema Consolidation
The `activities` table will be expanded to become a "Super-Entity" for all interaction logging.

**New/Modified Columns in `activities`**:
-   `customer_id`: foreignId (nullable)
-   `contact_id`: foreignId (nullable)
-   `visit_started_at`: datetime (nullable)
-   `visit_ended_at`: datetime (nullable)
-   `location`: string (nullable)
-   `purpose`: string (nullable)
-   `expectations`: text (nullable)
-   `targets`: text (nullable)
-   `stakeholder_feedback`: text (nullable)
-   `is_worth_keeping`: boolean (default: true)
-   `confidence_level`: integer (default: 0)
-   `next_contact_date`: date (nullable)
-   `follow_up_notes`: text (nullable)
-   `meeting_link`: string (nullable)
-   `messaging_platform`: string (nullable)

### 2. Model Refactoring
-   **Activity Model**: 
    -   Will include relationships to `Customer`, `Contact`, `Project`, and `User`.
    -   Will handle confidence level logic (if specific to the activity/visit context).
-   **Visit Model**: Removed.

### 3. Data Migration Strategy
A migration will be created to:
1.  Add new columns to `activities`.
2.  Map and insert all rows from `visits` into `activities`.
    -   `visit_type` -> `type`
    -   `summary_notes` -> `description`
    -   `visit_started_at` -> `performed_at`
3.  Drop the `visits` table.

### 4. Filament UI Refactor
-   **ActivityResource**:
    -   The form will use conditional logic based on the `type`. 
    -   If the type is a "Visit" (e.g., In-person, Online Meeting), the full set of visit-specific fields (expectation, feedback, etc.) will be shown.
    -   The `Project` Strategic Checklist action will be moved to the `ActivityResource`.

## Trade-offs
-   **Table Breadth**: The `activities` table will have many columns. However, this is outweighed by the simplicity of having a single stream of history for a project or customer.
-   **Polymorphism vs Flat**: We are choosing a flat table with nullable columns over a polymorphic relationship to keep queries simple and performant for reporting.
