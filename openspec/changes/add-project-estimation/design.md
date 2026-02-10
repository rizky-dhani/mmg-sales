# Design: Project Estimation Fields

## Data Model Changes

### `projects` table
We will add the following columns:
- `estimated_revenue` (decimal, 16, 2, nullable): The expected revenue.
- `estimated_completion_date` (date, nullable): The target finish date.

## Model Changes
### `Project` model
- Add new fields to `$fillable`.
- Add casts for `estimated_revenue` and `estimated_completion_date`.

## UI Changes
### Filament `ProjectResource`
- Add a new `Section` or `Group` titled "Estimation" in the form.
- Include `TextInput` for `estimated_revenue` (monetary format).
- Include `DatePicker` for `estimated_completion_date`.
- Add these columns to the `ListProjects` table view for quick reference.

## Alternatives Considered
- **Separate `estimations` table**: Overkill for a few fields. Keeping them on the `projects` table is simpler and fits the current architecture.
- **Using existing fields**: `estimated_value` is currently used, but the user specifically asked for "Expected Revenue", implying they might want both (Gross Value vs Net Revenue).
