# Proposal: Add Estimation to Project Resource

## Problem
Currently, the `Project` resource lacks specific fields for detailed financial and temporal estimation. While it has `estimated_value` and `expected_closing_date`, these are often used for general lead tracking. The user requires more granular "Estimation" data, including expected revenue and time to finish, to improve project planning and financial forecasting.

## Proposed Solution
We will extend the `projects` table and the `Project` model to include:
- `estimated_revenue`: The specific revenue expected from the project (might differ from total value).
- `estimated_completion_date`: When the project is expected to be fully finished/delivered.

We will also update the Filament `ProjectResource` to display and allow editing of these fields in a dedicated "Estimation" section.

## Benefits
- More accurate financial forecasting.
- Better visibility into project timelines.
