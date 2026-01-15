# Design: User Visits Integration

## Overview
We will leverage Filament's Infolist and RelationManager systems to provide a multi-layered view of a User's visit history.

## Components
1. **UserInfolist Update**:
   - Add a new `Section` titled "Latest Visits" to `UserInfolist`.
   - Use a `RepeatableEntry` to show the latest 5 visits.
   - Fields to include: `visit_started_at`, `company.facility_name`, `purpose`.

2. **VisitsRelationManager**:
   - Location: `App\Filament\Resources\Users\RelationManagers\VisitsRelationManager`.
   - Table configuration: Use `VisitsTable::configure()` but remove the `user.name` column as it is redundant in this context.
   - Enable standard actions (View, Edit, Delete).

## Considerations
- **Latest Visits**: Limited to 5 records, ordered by `visit_started_at` descending.
- **Redundancy**: The "Sales Rep" column in `VisitsTable` should be hidden when displayed within the `UserResource` context.
