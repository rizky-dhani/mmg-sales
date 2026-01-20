# Tasks: Add Visits Export to Excel (Maatwebsite/Excel)

- [ ] Create Export classes <!-- id: 0 -->
  - Implement `App\Exports\VisitsExport`.
  - Implement `App\Exports\VisitsMultiSheetExport`.
- [ ] Add Export actions to `VisitsTable` <!-- id: 1 -->
  - Replace `filament-excel` actions with custom Filament Actions.
  - Implement logic to fetch the filtered query and pass it to the exports.
- [x] Refine data mapping <!-- id: 2 -->
  - Ensure relations (user, company) are correctly exported.
- [x] Apply styling (AutoWidth, AutoHeight/Wrap, Alignment) <!-- id: 5 -->
  - Implement `ShouldAutoSize` and `WithStyles` in `VisitsExport`.
- [x] Cleanup <!-- id: 3 -->
  - Remove `pxlrbt/filament-excel` from `composer.json`.
- [ ] Verify functionality <!-- id: 4 -->
  - Test all export modes (Standard, by Rep, by Company).
  - Verify date range filters.