# Tasks: Add Project Estimation

- [x] Create migration to add estimation fields to `projects` table <!-- id: 0 -->
    - Add `estimated_revenue`, `estimated_completion_date`
- [x] Update `Project` model <!-- id: 1 -->
    - Update `$fillable` and `$casts`
- [x] Update Filament `ProjectResource` form <!-- id: 2 -->
    - Add "Estimation" section with new fields
- [x] Update Filament `ProjectResource` table <!-- id: 3 -->
    - Add columns for `estimated_revenue` and `estimated_completion_date`
- [x] Verify changes with a feature test <!-- id: 4 -->
    - Ensure fields are saved and displayed correctly
