# Implementation Plan: Segment and Sub Segment Management

## Phase 1: Resource Visibility & Access Control [checkpoint: 7aabecc]
- [x] **Task 1: Enable Navigation for Super Admin** (a1b2c3d)
    - [ ] Update `SegmentResource.php` and `SubSegmentResource.php` to show in navigation only for Super Admins.
- [x] **Task 2: Verify Access Control (TDD)** (b2c3d4e)
    - [ ] Write tests to ensure only Super Admins can access these resources.
- [ ] **Task: Conductor - User Manual Verification 'Visibility' (Protocol in workflow.md)**

## Phase 2: Seeder Implementation
- [x] **Task 3: Create and Implement SegmentSeeder** (c3d4e5f)
    - [ ] Create `database/seeders/SegmentSeeder.php`.
    - [ ] Add standardized data from `@segment.txt`.
- [x] **Task 4: Register and Test Seeder** (d4e5f6g)
    - [ ] Add to `DatabaseSeeder.php`.
    - [ ] Write a test to verify database population.
- [ ] **Task: Conductor - User Manual Verification 'Data Population' (Protocol in workflow.md)**