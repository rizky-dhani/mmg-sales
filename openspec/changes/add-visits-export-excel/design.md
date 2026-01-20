# Design: Visits Excel Export (Maatwebsite/Excel)

## Overview
We will implement direct Laravel Excel exports to provide maximum control over sheet generation and data formatting.

## Implementation Details
1. **Export Classes**:
   - `App\Exports\VisitsExport`: Implements `FromQuery`, `WithHeadings`, `WithMapping`. Handles standard flat exports.
   - `App\Exports\VisitsMultiSheetExport`: Implements `WithMultipleSheets`. Dynamically generates instances of a sub-export class (e.g., `VisitSheet`) for each group.

2. **Dynamic Sheet Generation**:
   - The action will determine the unique grouping keys (e.g., `user_id` or `company_id`) from the current filtered query.
   - For each key, a sheet will be created with its own filtered subset of the data.

3. **Styling and Formatting**:
   - Implement `ShouldAutoSize` for automatic column widths.
   - Implement `WithStyles` to:
     - Make headers bold.
     - Center align all content (horizontal and vertical).
     - Enable text wrapping (allowing for automatic row height adjustments based on content).

## Considerations
- **Memory Management**: For multi-sheet exports, we must be careful with large datasets. We will use `FromQuery` to leverage database-level filtering.
- **Naming**: Sheet names will be truncated to 31 characters to comply with Excel standards.
