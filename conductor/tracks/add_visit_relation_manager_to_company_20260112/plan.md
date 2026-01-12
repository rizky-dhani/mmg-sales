# Implementation Plan: Add Visit Relation Manager to Company

## Phase 1: Model & Infrastructure [checkpoint: 05385b3]
- [x] **Task 1: Add visits relationship to Company Model** (a1b2c3d)
    - [ ] Update `app/Models/Company.php` to include the `visits()` relationship.
- [x] **Task 2: Generate Relation Manager** (b2c3d4e)
    - [ ] Create `app/Filament/Resources/Companies/RelationManagers/VisitsRelationManager.php` using Artisan.
- [ ] **Task: Conductor - User Manual Verification 'Model & Infrastructure' (Protocol in workflow.md)**

## Phase 2: Relation Manager Configuration (TDD)
- [~] **Task 3: Configure Table Columns & Actions**
    - [ ] **Red:** Write a test to ensure the relation manager is configured with the correct columns and only the `ViewAction`.
    - [ ] **Green:** Implement table columns (User, Date, Purpose, Feedback) and enable `ViewAction`.
    - [ ] Explicitly disable `Create`, `Edit`, and `Delete` actions.
- [ ] **Task: Conductor - User Manual Verification 'Relation Manager Configuration' (Protocol in workflow.md)**

## Phase 3: Resource Integration
- [x] **Task 4: Register Relation Manager in CompanyResource** (c3d4e5f)
    - [ ] Update `app/Filament/Resources/Companies/CompanyResource.php` to include `VisitsRelationManager`.
    - [ ] Ensure it only displays on the `ViewCompany` page by checking the active page context.
- [ ] **Task: Conductor - User Manual Verification 'Resource Integration' (Protocol in workflow.md)**