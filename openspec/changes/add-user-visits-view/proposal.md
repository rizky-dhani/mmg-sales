# Proposal: Add User Visit View

## Goal
Enhance the User view by adding a dedicated section for latest visits and a Relation Manager for all associated visits.

## Scope
- Modify `UserInfolist` to include a "Latest Visits" section showing the 5 most recent visits.
- Create `VisitsRelationManager` for the `UserResource`.
- Register the `VisitsRelationManager` in `UserResource`.

## Expected Outcome
When viewing a User, administrators can see their 5 most recent visits directly in the profile view and access a full, searchable list of visits in the "Visits" tab.
