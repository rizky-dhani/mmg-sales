# Capability: Project Domain Refactoring

## MODIFIED Requirements

### Requirement: Domain Terminology
All occurrences of the term "Lead" in the system MUST be replaced with "Project" to reflect a strategic, milestone-based sales approach.

#### Scenario: Database Table Renaming
- **GIVEN** a database with `leads` and `lead_milestone` tables
- **WHEN** the migration is executed
- **THEN** the tables MUST be renamed to `projects` and `project_milestone` respectively.

#### Scenario: Foreign Key Updating
- **GIVEN** tables referencing `leads` via `lead_id`
- **WHEN** the migration is executed
- **THEN** those columns MUST be renamed to `project_id`.

#### Scenario: UI Label Updating
- **GIVEN** the Filament admin panel
- **WHEN** a user navigates to the sales section
- **THEN** they MUST see "Projects" in the navigation menu and page headers instead of "Leads".

#### Scenario: Model Class Renaming
- **GIVEN** the PHP codebase
- **WHEN** the refactoring is applied
- **THEN** the `Lead` model MUST be renamed to `Project` and follow appropriate namespacing.

#### Scenario: Policy and Permission Renaming
- **GIVEN** the `LeadPolicy` and related permissions
- **WHEN** the refactoring is applied
- **THEN** they MUST be renamed to `ProjectPolicy` and use project-based permission strings (e.g., `view_any_project`).
