# Spec Delta: Project Estimation

## ADDED Requirements

### Requirement: Track Project Estimation
The system must allow users to record and track estimated financial and temporal data for each project.

#### Scenario: Update project estimation
Given a user is on the "Edit Project" page
When they enter values for "Estimated Revenue" and "Estimated Completion Date"
Then the system must save these values correctly to the database.

#### Scenario: View estimation data in project list
Given a user is on the "Projects" list page
Then they should see columns for "Estimated Revenue" and "Estimated Completion Date" for each project.
