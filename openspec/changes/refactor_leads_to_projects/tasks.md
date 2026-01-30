# Tasks: Refactor Leads to Projects

## 1. Preparation
- [x] Confirm no active users are using the system to avoid session issues during rename. <!-- id: 0 -->

## 2. Database Migration
- [x] Create a migration to rename `leads` to `projects`. <!-- id: 1 -->
- [x] Create a migration to rename `lead_milestone` to `project_milestone`. <!-- id: 2 -->
- [x] Create a migration to rename `lead_id` to `project_id` in `project_milestone`. <!-- id: 3 -->
- [x] Create a migration to rename `lead_id` to `project_id` in `activities`. <!-- id: 4 -->
- [x] Create a migration to rename `lead_id` to `project_id` in `orders`. <!-- id: 5 -->
- [x] Run migrations and verify table structure. <!-- id: 6 -->

## 3. Model Refactoring
- [x] Rename `app/Models/Lead.php` to `app/Models/Project.php` and update class name. <!-- id: 7 -->
- [x] Rename `app/Models/LeadMilestone.php` to `app/Models/ProjectMilestone.php` and update class name. <!-- id: 8 -->
- [x] Update relationship methods in `Project`, `ProjectMilestone`, `Activity`, `Order`, `Customer`, and `Milestone`. <!-- id: 9 -->
- [x] Rename `app/Observers/OrderObserver.php` and `app/Observers/ActivityObserver.php` references from `lead` to `project`. <!-- id: 10 -->

## 4. Filament Admin Refactoring
- [x] Rename `app/Filament/Resources/LeadResource.php` to `app/Filament/Resources/ProjectResource.php`. <!-- id: 11 -->
- [x] Update `ProjectResource` class name, labels, and navigation. <!-- id: 12 -->
- [x] Rename `app/Filament/Resources/Leads/` directory to `app/Filament/Resources/Projects/`. <!-- id: 13 -->
- [x] Update all pages and widgets within the resource to reference `Project`. <!-- id: 14 -->
- [x] Update `app/Filament/Widgets/` that reference leads (e.g., `LeadBoard`, `LeadStatusOverview`). <!-- id: 15 -->

## 5. Policies & Permissions
- [x] Rename `app/Policies/LeadPolicy.php` to `app/Policies/ProjectPolicy.php`. <!-- id: 16 -->
- [x] Update `RolesAndPermissionsSeeder` to use `project` permissions. <!-- id: 17 -->
- [x] Re-run permission seeder. <!-- id: 18 -->

## 6. Cleanup & Verification
- [x] Search and replace remaining "lead" and "Lead" occurrences in the codebase. <!-- id: 19 -->
- [x] Update tests in `tests/Feature/` and `tests/Unit/` to reference `Project`. <!-- id: 20 -->
- [ ] Run all tests and ensure they pass. <!-- id: 21 -->
- [x] Run `vendor/bin/pint` to ensure code style consistency. <!-- id: 22 -->