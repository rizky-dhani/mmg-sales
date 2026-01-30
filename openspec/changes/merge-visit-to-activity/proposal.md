# Proposal: Merge Visit into Activity

## Problem
The system currently maintains two separate entities for logging interactions: `Visit` and `Activity`. This redundancy leads to fragmented data, inconsistent tracking, and dual maintenance of similar features (like confidence levels or notes). Having separate models for "Sales Reports" and "Project Activities" complicates the strategic overview of customer engagement.

## Proposed Solution
Unify all interactions into a single `Activity` entity. 
-   Enhance the `activities` table with fields from the `visits` table (e.g., `customer_id`, `contact_id`, `location`, `confidence_level`).
-   Standardize the `Activity` model to support both general activities and detailed sales visits through the `type` field.
-   Decommission the `Visit` model, migration, and Filament resource.
-   Ensure a direct and consistent link to `Projects` by ensuring all activities use `project_id`.

## Change ID
`merge-visit-to-activity`

## Requirements
-   The `activities` table SHALL incorporate all functional fields currently present in the `visits` table.
-   The `Activity` model SHALL be the sole entity for logging customer and project interactions.
-   All existing `Visit` data MUST be migrated to the `activities` table.
-   The `Visit` model, table, and Filament resource SHALL be removed.
-   The `ActivityResource` SHALL be updated to provide a user experience equivalent to the current `VisitResource`, including support for the Strategic Checklist and confidence level updates.
