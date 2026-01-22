# Spec: Update Visit Fields and Form

## ADDED Requirements

### Requirement: Visit Type Classification
The system MUST support classifying visits by their conduct method.
-   **Field**: `visit_type`
-   **Allowed Values**: `in_person`, `video_call`, `phone_call`, `messaging`.

#### Scenario: Selecting Visit Type
-   Given a user is creating a new visit.
-   When they select `video_call` as the `visit_type`.
-   Then the `meeting_link` field MUST become visible.

### Requirement: Confidence Level Tracking
The system MUST allow representatives to record their confidence in the visit outcome.
-   **Field**: `confidence_level`
-   **Range**: 0 to 100.
-   **Granularity**: Steps of 5.

#### Scenario: Adjusting Confidence
-   Given a user is on the visit form.
-   When they interact with the confidence level slider.
-   Then the value MUST change in increments of 5 and show a tooltip with the current value.

### Requirement: Conditional Fields
Fields relevant to specific visit types MUST be displayed conditionally.
-   `meeting_link` MUST only be visible for `video_call`.
-   `messaging_platform` MUST only be visible for `messaging`.

#### Scenario: Hiding Irrelevant Fields
-   Given a user has selected `phone_call` as the `visit_type`.
-   Then the `meeting_link` and `messaging_platform` fields MUST be hidden.