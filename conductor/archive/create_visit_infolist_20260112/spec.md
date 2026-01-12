# Track Spec: Create VisitInfolist for Visit Resource

## Overview
This track implements a dedicated `Infolist` for the `Visit` resource in Filament. This will provide a clean, read-only view of visit details, including logistics, strategic intent, and stakeholder feedback.

## Functional Requirements
### 1. Infolist Schema Definition
- Create `App\Filament\Resources\Visits\Schemas\VisitInfolist`.
- Use `Filament\Infolists\Components\Section` to group related information.
- **Sections:**
    - **Visit Logistics:** Company (with link), Contact Person, Start/End times, Location, and Sales Representative.
    - **Strategic Intent:** Purpose, Expectations, and Targets.
    - **Outcome:** Summary notes.
    - **Stakeholder Review:** Feedback and "Is worth keeping" status (visible to authorized roles).

### 2. Resource Integration
- Register the `infolist()` method in `App\Filament\Resources\Visits\VisitResource`.
- Ensure the `ViewVisit` page is correctly registered to use this infolist.

## Acceptance Criteria
- [ ] Viewing a Visit record displays the data organized into the defined sections.
- [ ] Company and User names are displayed correctly through relationships.
- [ ] Date/time fields are formatted for readability.
- [ ] Stakeholder feedback section is only visible to Super Admin or Board of Director roles.
- [ ] The view page renders without errors.

## Out of Scope
- Modifying the existing `VisitForm` or `VisitsTable`.
- Changing the database schema.
