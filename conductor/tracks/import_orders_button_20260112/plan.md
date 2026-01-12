# Implementation Plan: Import Orders via Excel

## Phase 1: Preparation & Infrastructure [checkpoint: 8ab56b3]
- [x] **Task 1: Project Setup & Dependencies** (e1a2b3c)
    - [ ] Research and confirm the best library for Excel handling (likely `laravel-excel` / `maatwebsite/excel`).
    - [ ] Install necessary dependencies via Composer.
    - [ ] Create the background job class: `app/Jobs/ImportOrdersJob.php`.
- [x] **Task 2: Database & Model Preparation** (f4e5d6c)
    - [ ] Ensure `Order` and `OrderItem` models have appropriate mass-assignment protections.
    - [ ] Verify existence of unique identifiers (e.g., `order_number`) for duplicate checking.
- [ ] **Task: Conductor - User Manual Verification 'Preparation & Infrastructure' (Protocol in workflow.md)**

## Phase 2: UI Implementation
- [ ] **Task 3: Orders Resource UI Enhancements**
    - [ ] Add the "Import" Header Action to `App\Filament\Resources\OrderResource\Pages\ListOrders`.
    - [ ] Implement the upload modal and file validation (MIME type: `.xlsx`).
    - [ ] Add a "Download Template" action to the modal pointing to the existing template file.
- [ ] **Task: Conductor - User Manual Verification 'UI Implementation' (Protocol in workflow.md)**

## Phase 3: Core Import Logic & Validation (TDD)
- [ ] **Task 4: Excel Parsing & Structure Validation**
    - [ ] **Red:** Write tests for parsing files that match vs. don't match the existing template structure.
    - [ ] **Green:** Implement parsing logic in the `ImportOrdersJob`.
- [ ] **Task 5: Atomic Business Validation & Existence Checks**
    - [ ] **Red:** Write tests for cross-referencing Customers, Products, and handling missing required fields.
    - [ ] **Green:** Implement validation logic with database existence checks.
    - [ ] Wrap the entire process in a database transaction to ensure atomicity.
- [ ] **Task 6: Duplicate Handling Logic**
    - [ ] **Red:** Write tests for skipping existing orders based on unique identifiers.
    - [ ] **Green:** Implement the "Skip Duplicates" logic.
- [ ] **Task: Conductor - User Manual Verification 'Core Import Logic & Validation' (Protocol in workflow.md)**

## Phase 4: Asynchronous Integration & Notifications
- [ ] **Task 7: Background Job Wiring**
    - [ ] Connect the Filament UI upload to dispatch the `ImportOrdersJob`.
    - [ ] Implement background notification logic using `Filament\Notifications\Notification`.
- [ ] **Task 8: Error Reporting & Feedback**
    - [ ] Ensure the user receives clear error messages if the atomic import fails.
- [ ] **Task: Conductor - User Manual Verification 'Asynchronous Processing & Notifications' (Protocol in workflow.md)**