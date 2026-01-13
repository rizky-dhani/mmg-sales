# Implementation Plan: Add VisitWidget

## Phase 1: Core Logic & Scoping [x]
- [x] Task: Research existing hierarchy implementation in Policies/Scopes.
- [x] Task: Create a dedicated `VisitScopeService` to handle hierarchical data retrieval for widgets.
- [x] Task: Write unit tests for `VisitScopeService` to verify:
    - [x] Sales Reps see only their own visits.
    - [x] Managers see their own and subordinates' visits.
    - [x] Heads/BOD see all visits.
- [x] Task: Conductor - User Manual Verification 'Core Logic & Scoping' (Protocol in workflow.md)

## Phase 2: Dashboard Widget Implementation [ ]
- [ ] Task: Generate `VisitOverviewWidget` (Filament Stat & Table widget).
- [ ] Task: Write tests for `VisitOverviewWidget` data calculations (Monthly Growth, Stats).
- [ ] Task: Implement the Stats section (Total, Monthly, Growth %).
- [ ] Task: Implement the "Recent Visits" table (Last 5-10 entries).
- [ ] Task: Implement the "Sales Rep Leaderboard" (Visits per subordinate).
- [ ] Task: Register `VisitOverviewWidget` in `app/Providers/Filament/AdminPanelProvider.php` or `Dashboard.php`.
- [ ] Task: Conductor - User Manual Verification 'Dashboard Widget Implementation' (Protocol in workflow.md)

## Phase 3: Contextual Company Widget [ ]
- [ ] Task: Generate `CompanyVisitWidget`.
- [ ] Task: Write tests for `CompanyVisitWidget` filtering by active Company record.
- [ ] Task: Implement contextual Stats: "Last Visit" and "Total Visits for this Company".
- [ ] Task: Implement contextual "Recent Activity" table.
- [ ] Task: Add `CompanyVisitWidget` to `CompanyResource` view page.
- [ ] Task: Conductor - User Manual Verification 'Contextual Company Widget' (Protocol in workflow.md)

## Phase 4: Final Polishing & Verification [ ]
- [ ] Task: Run all tests to ensure no regressions.
- [ ] Task: Manual verification of widget visibility across different roles (SR, ASM, Head).
- [ ] Task: Ensure all code matches project style using `vendor/bin/pint`.
- [ ] Task: Conductor - User Manual Verification 'Final Polishing & Verification' (Protocol in workflow.md)
