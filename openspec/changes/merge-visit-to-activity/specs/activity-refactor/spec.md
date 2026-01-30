# Spec Delta: Unifying Visits and Activities

## REMOVED Requirements

### Requirement: Visit Tracking
The dedicated `Visit` entity for sales reporting MUST be removed in favor of the unified `Activity` system.

## MODIFIED Requirements

### Requirement: Unified Interaction Logging
The `Activity` system SHALL serve as the single source of truth for all customer and project interactions.

#### Scenario: Logging a Sales Visit as an Activity
- **Given** I am creating a new Activity.
- **When** I select "In-person Meeting" as the type.
- **Then** the form MUST present fields for Location, Purpose, and Stakeholder Feedback.
- **And** the activity MUST be linkable to a Project, Customer, and Contact.

### Requirement: Activity-Project Synchronization
Activities MUST maintain a direct link to Projects to track progress and history.

#### Scenario: Activity Linkage
- **Given** a Project exists for "Customer B".
- **When** I log an Activity for "Customer B".
- **Then** I SHOULD be able to associate this Activity with that specific Project.
- **And** the Activity MUST appear on the Project's history timeline.

### Requirement: Checklist Management in Activity Context
Strategic Checklist updates SHALL be performed within the `Activity` resource.

#### Scenario: Updating Project Checklist from Activity
- **Given** an Activity is linked to "Project Alpha".
- **When** I trigger the "Checklist" action from the Activity view.
- **Then** I MUST be able to update "Project Alpha"'s milestones directly.
