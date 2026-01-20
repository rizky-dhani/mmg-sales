# Capability: Visits Excel Export

## ADDED Requirements

### Requirement: Export Formats
The system MUST offer multiple export formats: "Standard", "By Representative", and "By Company".

#### Scenario: Exporting grouped by Sales Rep
Given I am on the Visits list page
When I select "Export by Representative"
Then an Excel file SHOULD be generated with one sheet for each Sales Rep who has visit records in the current selection.

### Requirement: Date Range Filtering
The export MUST respect the active date range filters applied to the table.

#### Scenario: Exporting a specific month
Given I have filtered visits for January 2026
When I perform an export
Then the resulting file MUST only contain records from January 2026.

### Requirement: Visual Formatting
The exported file MUST be professionally formatted for readability.

#### Scenario: Verifying sheet styling
Given I have exported visits
When I open the Excel file
Then columns SHOULD be automatically sized to fit the content.
And all text SHOULD be center-aligned.
And headers SHOULD be bold.
And long text (like purpose or summary) SHOULD wrap within the cell.
