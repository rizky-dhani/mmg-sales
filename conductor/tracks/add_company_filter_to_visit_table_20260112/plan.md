# Implementation Plan: Add Company Filter to VisitTable

## Phase 1: Filter Implementation
- [ ] **Task 1: Add Company Filter to VisitsTable**
    - [ ] Update `app/Filament/Resources/Visits/Tables/VisitsTable.php` to add `SelectFilter::make('company_id')`.
- [ ] **Task 2: Verify Filter Functionality (TDD)**
    - [ ] Write a feature test to ensure the Visit table can be filtered by company.
- [ ] **Task: Conductor - User Manual Verification 'Filter Implementation' (Protocol in workflow.md)**
