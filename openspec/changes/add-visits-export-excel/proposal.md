# Proposal: Add Export to Excel for Visits (Maatwebsite/Excel)

## Goal
Implement a flexible "Export to Excel" feature for the Visits resource using `maatwebsite/excel`, providing standard and segmented (multi-sheet) export options.

## Scope
- Create custom Export classes using `maatwebsite/excel`.
- Implement a "Standard Export" (single sheet).
- Implement a "Grouped Export" (multi-sheet by Sales Rep or Company).
- Integrate these exports into the Visits table via custom Filament Header and Bulk actions.
- Ensure active filters (especially date range) are respected.

## Expected Outcome
Users can download visit data in .xlsx format, choosing between a flat list or a file organized into sheets by representative or facility.
