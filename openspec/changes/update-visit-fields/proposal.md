# Proposal: Update Visit Fields and Form

Update the `Visit` model to include structured visit types and a confidence level metrics. The `VisitForm` will be enhanced with conditional logic and interactive UI components (Slider).

## Problem
Currently, visits lack structured classification of how they were conducted (visit type) and a quantitative measure of the representative's confidence in the outcome.

## Proposed Solution
1.  **Database Updates**:
    -   Add `visit_type` column (In-person, Video Call, Phone Call, Messaging).
    -   Add `meeting_link` column (conditional for Video Call).
    -   Add `messaging_platform` column (conditional for Messaging).
    -   Add `confidence_level` column (integer 0-100).
2.  **Filament Form Updates**:
    -   Implement `visit_type` as a Select dropdown.
    -   Use `hidden()` or `visible()` conditional logic for `meeting_link` and `messaging_platform` based on `visit_type`.
    -   Implement `confidence_level` using a `Slider` component with steps of 5 and tooltips.
    -   Ensure all relationship fields (`customer_id`, `contact_id`) use optimized `Select` components.

## Goals
-   Provide better analytics on sales activities by visit type.
-   Capture representative's confidence in visit outcomes.
-   Improve UI/UX with interactive components and conditional fields.
