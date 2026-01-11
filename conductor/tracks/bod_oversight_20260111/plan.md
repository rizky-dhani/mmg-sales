# Plan: Implement Board of Director Role and Global Oversight Access

This plan outlines the steps to implement and verify the Board of Director role with global oversight access.

## Phase 1: Database & Seeders
Establish the foundational data structures for the BOD role.

- [ ] **Task 1: Verify Role and Position Seeders**
  - Verify `RolesAndPermissionsSeeder.php` correctly creates the 'Board of Director' role with only view permissions.
  - Verify `DepartmentSeeder.php` includes the 'MGMT' department.
  - Verify `PositionSeeder.php` correctly places BOD at Level 0 and as the parent of HEAD.
- [ ] **Task 2: Create Test to Verify Seeder Output**
  - Write a feature test to run seeders and assert the existence of the BOD role, position, and its permissions.
- [ ] **Task 3: Implement Seeder Verification Test**
  - Run the test and ensure it passes with 99% coverage for the seeder logic.
- [ ] **Task: Conductor - User Manual Verification 'Phase 1: Database & Seeders' (Protocol in workflow.md)**

## Phase 2: Authorization & Policies
Ensure strict read-only enforcement across the application.

- [ ] **Task 4: Write Tests for BOD Read-Only Enforcement**
  - Create a feature test that simulates a BOD user attempting to view, create, update, and delete resources (Lead, Order, Customer).
  - Assert success for `view` and `view_any`, and failure for others.
- [ ] **Task 5: Refine BasePolicy for BOD Role**
  - Ensure `BasePolicy` explicitly handles the BOD role if the permission-based checks are insufficient or to provide an extra layer of security.
- [ ] **Task 6: Implement BOD Authorization Logic**
  - Ensure all model policies inherit from `BasePolicy` or correctly check the BOD permissions.
- [ ] **Task 7: Run Authorization Tests**
  - Run tests and ensure 99% coverage for policy logic.
- [ ] **Task: Conductor - User Manual Verification 'Phase 2: Authorization & Policies' (Protocol in workflow.md)**

## Phase 3: Filament UI Verification
Verify the user experience and UI restrictions for the BOD role.

- [ ] **Task 8: Write Browser Tests for BOD Panel Access**
  - Create a Pest browser test to log in as a BOD user.
  - Assert that navigation items are visible but "Create" and "Edit" buttons are absent.
- [ ] **Task 9: Verify Resource Table Actions**
  - Ensure table actions in Filament resources correctly reflect the read-only status for BOD.
- [ ] **Task 10: Run UI Verification Tests**
  - Run browser tests and ensure all pass.
- [ ] **Task: Conductor - User Manual Verification 'Phase 3: Filament UI Verification' (Protocol in workflow.md)**
