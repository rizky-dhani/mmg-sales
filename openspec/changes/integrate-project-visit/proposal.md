# Proposal: Integrate Project with Visit

## Problem
Currently, `Projects` (formerly Leads) and `Visits` (Sales Reports) are disconnected. A `Visit` is often part of a larger `Project` lifecycle, but there is no direct link between them. Users have to manually create `Activity` records for projects, even if they have already logged a detailed `Visit`. Furthermore, updating project progress (Confidence Checklist) requires navigating away from the `Visit` record.

## Proposed Solution
Establish a direct relationship between `Visits` and `Projects`. This will allow:
1.  Linking a visit to a specific project.
2.  Automated creation of `Activity` records on the project timeline whenever a visit is logged.
3.  Updating the project's strategic checklist directly from the visit context.

## Change ID
`integrate-project-visit`

## Requirements
- `Visit` model must have an optional `project_id` field.
- `Visit` form must allow selecting a project belonging to the same customer.
- Saving a `Visit` linked to a `Project` must create or update a corresponding `Activity` for that project.
- The `Project` checklist action must be accessible from the `Visit` resource.
