# Capability: User Visits View

## ADDED Requirements

### Requirement: Latest Visits Section
The User view MUST include a section displaying the most recent visits by that user.

#### Scenario: Displaying Latest Visits
Given a User with multiple visits
When I view the User profile
Then I should see a "Latest Visits" section
And it should list up to 5 most recent visits showing date, company name, and purpose.

### Requirement: Visits Relation Manager
The User resource MUST include a relation manager for visits to allow full management of the user's visit history.

#### Scenario: Managing Visits from User View
Given I am viewing a User
When I click on the "Visits" tab
Then I should see a table of all visits associated with that user
And I should be able to create, edit, and view visits from this tab.
And the "Sales Rep" column should be hidden as it is redundant.