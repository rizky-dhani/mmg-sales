# Plan: Implement Board of Director Role and Global Oversight Access

This plan outlines the steps to implement and verify the Board of Director role with global oversight access.

## Phase 1: Database & Seeders [checkpoint: 81276c9]
Establish the foundational data structures for the BOD role.

- [x] **Task 1: Verify Role and Position Seeders** (91e2a9b)
  - Verify `RolesAndPermissionsSeeder.php` correctly creates the 'Board of Director' role with only view permissions.
  - Verify `DepartmentSeeder.php` includes the 'MGMT' department.
  - Verify `PositionSeeder.php` correctly places BOD at Level 0 and as the parent of HEAD.
- [x] **Task 2: Create Test to Verify Seeder Output** (2060072)
  - Write a feature test to run seeders and assert the existence of the BOD role, position, and its permissions.
- [x] **Task 3: Implement Seeder Verification Test** (2060072)
  - Run the test and ensure it passes with 99% coverage for the seeder logic.
- [x] **Task: Conductor - User Manual Verification 'Phase 1: Database & Seeders' (Protocol in workflow.md)** (81276c9)

## Phase 2: Authorization & Policies [checkpoint: 3618e61]
Ensure strict read-only enforcement across the application.

- [x] **Task 4: Write Tests for BOD Read-Only Enforcement** (8e3a47b)
  - Create a feature test that simulates a BOD user attempting to view, create, update, and delete resources (Lead, Order, Customer).
  - Assert success for `view` and `view_any`, and failure for others.
- [x] **Task 5: Refine BasePolicy for BOD Role** (8e3a47b)
  - Ensure `BasePolicy` explicitly handles the BOD role if the permission-based checks are insufficient or to provide an extra layer of security.
- [x] **Task 6: Implement BOD Authorization Logic** (8e3a47b)
  - Ensure all model policies inherit from `BasePolicy` or correctly check the BOD permissions.
- [x] **Task 7: Run Authorization Tests** (8e3a47b)
  - Run tests and ensure 99% coverage for policy logic.
- [x] **Task: Conductor - User Manual Verification 'Phase 2: Authorization & Policies' (Protocol in workflow.md)** (3618e61)

## Phase 3: Filament UI Verification
Verify the user experience and UI restrictions for the BOD role.

- [x] **Task 8: Write Feature Tests for BOD Panel Access** (76be9ea)
  - Create a feature test that attempts to visit Filament resource pages (List, Create, Edit) as a BOD user.
  - Assert that List pages are accessible, but Create and Edit pages return a forbidden status (403).
- [x] **Task 9: Verify Resource Table Actions** (76be9ea)
  - Use Filament's testing helpers to assert that "create", "edit", and "delete" actions are hidden for BOD users on resource tables.
- [x] **Task 10: Run UI Verification Tests** (76be9ea)
  - Run the feature tests and ensure all pass.
- [ ] **Task: Conductor - User Manual Verification 'Phase 3: Filament UI Verification' (Protocol in workflow.md)**
