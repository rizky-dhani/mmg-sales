# Track Spec: Add Visit Relation Manager to Company View

## Overview
This track adds a "Visits" Relation Manager to the `Company` resource in Filament. This will allow users to see a history of all visits associated with a specific company directly from its view page. Per user request, the relation manager will be read-only, providing only a "View" action for individual visits.

## Functional Requirements
### 1. Model Relationship
- Add a `HasMany` relationship named `visits()` to the `App\Models\Company` model.

### 2. Relation Manager Implementation
- Create `App\Filament\Resources\Companies\RelationManagers\VisitsRelationManager`.
- Configure the table to display key visit information:
    - **User:** The representative who performed the visit.
    - **Date:** Start time of the visit.
    - **Purpose:** Brief description of why the visit occurred.
    - **Status/Feedback:** Success or feedback notes.
- **Actions:**
    - Enable `ViewAction` to allow users to see full visit details.
    - **Disable** `CreateAction`, `EditAction`, and `DeleteAction` to ensure the list remains read-only from the Company perspective.

### 3. Integration
- Register the `VisitsRelationManager` in `App\Filament\Resources\Companies\CompanyResource`.
- Configure the relation manager to be visible **only on the View page** of the Company resource.

## Acceptance Criteria
- [ ] Viewing a Company record shows a "Visits" tab or section below the main infolist.
- [ ] The Visits table displays correct data for associated visit records.
- [ ] Clicking the "View" action on a visit row opens a modal or leads to the visit's detail page.
- [ ] No "Create", "Edit", or "Delete" buttons are visible within the Visits relation manager.
- [ ] The relation manager does NOT appear on the Company "Edit" page.

## Out of Scope
- Modifying the existing `VisitResource`.
- Adding new fields to the `Visit` model.