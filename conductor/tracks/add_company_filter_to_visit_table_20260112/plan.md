# Implementation Plan: Add Company Filter to VisitTable

## Phase 1: Filter Implementation [checkpoint: 88c1905]
- [x] **Task 1: Add Company Filter to VisitsTable** (a1b2c3d)
    - [ ] Update `app/Filament/Resources/Visits/Tables/VisitsTable.php` to add `SelectFilter::make('company_id')`.
- [x] **Task 2: Verify Filter Functionality (TDD)** (b2c3d4e)
    - [ ] Write a feature test to ensure the Visit table can be filtered by company.
- [ ] **Task: Conductor - User Manual Verification 'Filter Implementation' (Protocol in workflow.md)**
