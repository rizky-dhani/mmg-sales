# Proposal: Refactor Leads to Projects

## Problem
The term "Leads" implies a top-of-funnel, transactional contact. However, the system is evolving into a strategic tool where "Leads" actually represent multi-milestone "Projects" with long-term tracking and complex confidence scoring. Renaming "Leads" to "Projects" better reflects the strategic nature of the sales process in this application.

## Proposed Solution
Systematically rename all occurrences of "Lead" to "Project" (and "lead" to "project") across the codebase, database, and user interface. This ensures terminology alignment between the business logic and the implementation.

## Impact
### UI/UX
- Filament navigation menu will show "Projects" instead of "Leads".
- All headers, buttons, and labels referring to "Leads" will be updated to "Projects".

### Database
- Rename `leads` table to `projects`.
- Rename `lead_milestone` table to `project_milestone`.
- Rename foreign keys (e.g., `lead_id` to `project_id`) in `activities`, `orders`, `project_milestone`, etc.

### Codebase
- Rename models: `Lead` -> `Project`, `LeadMilestone` -> `ProjectMilestone`.
- Rename Filament resources: `LeadResource` -> `ProjectResource`.
- Update namespaces, imports, and variable names.
- Update policies and permissions.

## Risks & Mitigations
- **Data Loss**: Renaming tables and columns can be risky. Mitigation: Use standard Laravel migrations with `Schema::rename`.
- **Broken Links/References**: Hardcoded strings might be missed. Mitigation: Comprehensive grep search and automated tests.
- **Permissions**: Roles and permissions tied to the "lead" string need updating. Mitigation: Update `RolesAndPermissionsSeeder` and related policy strings.
