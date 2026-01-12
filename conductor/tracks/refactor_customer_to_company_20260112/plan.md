# Implementation Plan: Full Refactor from Customer to Company

## Phase 1: Database Schema Refactor [checkpoint: 88e8262]
- [x] **Task 1: Create Migration for Table Renaming** (a1b2c3d)
    - [ ] Rename `customers` to `companies`.
    - [ ] Rename `customer_groups` to `company_groups`.
- [x] **Task 2: Create Migration for Foreign Key Renaming** (b2c3d4e)
    - [ ] Update `orders` table keys.
    - [ ] Update `leads` table keys.
    - [ ] Update `contacts` table keys.
    - [ ] Update `users` table keys.
- [ ] **Task: Conductor - User Manual Verification 'Database Schema Refactor' (Protocol in workflow.md)**

## Phase 2: Model & Logic Refactor
- [ ] **Task 3: Rename Eloquent Models**
    - [ ] Rename `Customer.php` -> `Company.php`.
    - [ ] Rename `CustomerGroup.php` -> `CompanyGroup.php`.
- [ ] **Task 4: Update Model Relationships & Namespaces**
    - [ ] Update relationships in all related models (`Order`, `Lead`, `Contact`, `User`).
    - [ ] Global search and replace `Customer` with `Company` and `CustomerGroup` with `CompanyGroup` in PHP logic.
- [ ] **Task 5: Update Policies & RBAC**
    - [ ] Rename `CustomerPolicy` -> `CompanyPolicy`.
    - [ ] Rename `CustomerGroupPolicy` -> `CompanyGroupPolicy`.
    - [ ] Update permission registration logic.
- [ ] **Task: Conductor - User Manual Verification 'Model & Logic Refactor' (Protocol in workflow.md)**

## Phase 3: Filament UI Refactor
- [ ] **Task 6: Refactor Filament Resources**
    - [ ] Rename `Customers/` directory to `Companies/`.
    - [ ] Rename `CustomerGroups/` directory to `CompanyGroups/`.
    - [ ] Update all class names and labels within the resources.
- [ ] **Task 7: Navigation & UI Labels**
    - [ ] Enable navigation for `CompanyResource`.
    - [ ] Update labels in navigation groups and headers.
- [ ] **Task: Conductor - User Manual Verification 'Filament UI Refactor' (Protocol in workflow.md)**

## Phase 4: Verification & Cleanup
- [ ] **Task 8: Global Tests & Integrity Check**
    - [ ] Update existing tests to reference new models.
    - [ ] Run all tests to ensure no regressions.
- [ ] **Task: Conductor - User Manual Verification 'Verification & Cleanup' (Protocol in workflow.md)**