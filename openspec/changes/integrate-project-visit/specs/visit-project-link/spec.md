# Spec Delta: Visit and Project Integration

## MODIFIED Requirements

### Requirement: Link Visit to Project
Visits SHALL be optionally associated with a Project to track them as part of a larger sales effort.

#### Scenario: Selecting a Project in Visit Form
- **Given** I am filling out a Visit form for "Hospital A".
- **And** "Hospital A" has two active Projects: "MRI Upgrade" and "CT Scan Maintenance".
- **When** I click on the Project field.
- **Then** I should see "MRI Upgrade" and "CT Scan Maintenance" as options.
- **But** I should not see projects belonging to other customers.

### Requirement: Automated Project Activity Tracking
Logging a Visit for a Project MUST automatically update the Project's activity timeline.

#### Scenario: Activity Generation
- **Given** I am creating a Visit for the "MRI Upgrade" project.
- **And** I enter "Discussed pricing" in the summary notes.
- **When** I save the Visit.
- **Then** a new Activity should appear in the "MRI Upgrade" project timeline.
- **And** the Activity's description should match the Visit's summary notes.
- **And** the Activity's type should match the Visit's type (e.g., "In-person").

### Requirement: Checklist Management in Visit Context
Users SHALL be able to update the Strategic Checklist of a linked Project without leaving the Visit resource.

#### Scenario: Updating Checklist from Visit Table
- **Given** a Visit is linked to the "MRI Upgrade" project.
- **When** I click the "Checklist" action on the Visit row.
- **Then** the "MRI Upgrade" Strategic Checklist modal should open.
- **And** saving changes in the modal should update the Project's confidence level.
