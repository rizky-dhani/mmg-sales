# Track Spec: Add Company Filter to VisitTable

## Overview
This track adds a filter to the `Visit` resource's table in Filament, allowing users to filter visit records by the associated company.

## Functional Requirements
### 1. Table Filter
- Update `App\Filament\Resources\Visits\Tables\VisitsTable` to include a `SelectFilter` for the `company` relationship.
- The filter should:
    - Target the `company_id` column.
    - Use the `facility_name` from the `Company` model as labels.
    - Be searchable.
    - Be preloaded for better performance and UX.

## Acceptance Criteria
- [ ] Users can see a "Company" filter in the Visit table's filter dropdown.
- [ ] Selecting a company correctly filters the table to show only visits associated with that company.
- [ ] The filter dropdown allows searching for companies by name.

## Out of Scope
- Adding other filters (e.g., date range, representative) unless requested.
- Modifying the `Visit` model or schema.
