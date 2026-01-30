# Design: Refactoring Leads to Projects

## Overview
The refactoring involves a global rename of the "Lead" domain to "Project". This affects the database schema, backend logic, and frontend representation.

## Architectural Reasoning

### Domain Alignment
The "Project" terminology aligns more closely with the "Strategic Sales Management" goal defined in `openspec/specs/strategic_sales_management.md`. Projects have milestones and confidence levels, which are already implemented but currently attached to the "Lead" name.

### Refactoring Strategy
1. **Database Migrations**: Rename tables first, then columns. This maintains data integrity.
2. **Class & Namespace Renaming**: Follow standard PSR naming conventions. Rename files alongside classes.
3. **Filament Resource Update**: Update labels and navigation icons to represent a "Project" (e.g., using `Heroicon::Briefcase` or similar).
4. **Policy & Permission Synchronization**: Ensure that existing users with "lead" permissions can still access "projects".

## Trade-offs

### Migration vs. New Table
- **Rename**: Efficient and preserves history. Requires careful management of foreign keys.
- **New Table + Data Copy**: Safer but more complex and time-consuming.
- **Decision**: Rename existing tables to maintain continuous tracking and avoid complex data migration scripts.

### Automated vs. Manual Renaming
- **Automated (Grep/Sed)**: Fast but prone to errors (e.g., matching unrelated strings).
- **Manual/Semi-automated**: Safer.
- **Decision**: Use a combination of IDE refactoring tools where possible and targeted `rg`/`replace` calls with manual verification.

## Implementation Details

### Database Renames
- `leads` -> `projects`
- `lead_milestone` -> `project_milestone`
- Column `lead_id` -> `project_id` in:
    - `project_milestone`
    - `activities`
    - `orders`
    - `visits` (if exists)

### Model Renames
- `app/Models/Lead.php` -> `app/Models/Project.php`
- `app/Models/LeadMilestone.php` -> `app/Models/ProjectMilestone.php`

### Observer Renames
- `app/Observers/LeadObserver.php` (if exists) -> `app/Observers/ProjectObserver.php`
- Update `ActivityObserver` and `OrderObserver` to reference `Project`.

### Filament Renames
- `app/Filament/Resources/LeadResource.php` -> `app/Filament/Resources/ProjectResource.php`
- All sub-pages and related widgets.
