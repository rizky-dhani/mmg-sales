# Design: Integrate Project with Visit

## Architecture
This change bridges the gap between the `Visits` module and the `Projects` (Strategic Sales) module.

### 1. Database Schema
- **Table**: `visits`
- **Field**: `project_id` (foreignId, nullable, constrained to `projects`).

### 2. Activity Synchronization
To avoid data duplication, we will use a `VisitObserver`. 
- When a `Visit` is saved with a `project_id`:
    - Find or create an `Activity` record where `project_id` and a unique identifier (e.g., a new `visit_id` field in activities or a JSON metadata field) match.
    - Alternatively, we can just ensure that every Visit generates a fresh Activity. Since Activities are basically logs, we will link them.
    - **Refinement**: Add `visit_id` to the `activities` table to allow 1:1 synchronization and avoid duplicates if the Visit is edited multiple times.

### 3. Filament UI Integration
- **VisitForm**:
    - Add `Select::make('project_id')` that becomes visible/enabled after `customer_id` is selected.
    - Scoped via `->options(fn ($get) => Project::where('customer_id', $get('customer_id'))->pluck('title', 'id'))`.
- **Checklist Action**:
    - Port the `getChecklistAction` logic to `VisitResource`. It should only be visible if `project_id` is present on the record.

### 4. Logic Flow
1. Sales rep conducts a visit.
2. Rep creates a `Visit` record in Filament.
3. Rep selects the `Customer` and then selects the relevant `Project`.
4. Upon saving, a `VisitObserver` creates an `Activity` in the `Project` timeline.
5. Rep uses the "Checklist" action on the Visit table/page to update the Project's milestones based on the meeting outcome.

## Trade-offs
- **Redundancy**: Creating an `Activity` record from a `Visit` duplicates some data (summary notes -> activity description). However, this is necessary to maintain the `Project` activity feed as a single source of truth for all project-related events (emails, calls, visits).
- **Complexity**: Synchronizing edits between `Visit` and `Activity` adds overhead. We will prioritize "Visit as master" — changes to the Visit update the linked Activity.
