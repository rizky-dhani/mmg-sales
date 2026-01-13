# Implementation Plan: Add VisitWidget

## Phase 1: Core Logic & Scoping [x]
- [x] Task: Research existing hierarchy implementation in Policies/Scopes.
- [x] Task: Create a dedicated `VisitScopeService` to handle hierarchical data retrieval for widgets.
- [x] Task: Write unit tests for `VisitScopeService` to verify:
    - [x] Sales Reps see only their own visits.
    - [x] Managers see their own and subordinates' visits.
    - [x] Heads/BOD see all visits.
- [x] Task: Conductor - User Manual Verification 'Core Logic & Scoping' (Protocol in workflow.md)

## Phase 2: Dashboard Widget Implementation [x]
- [x] Task: Generate `VisitStatsWidget`, `RecentVisitsWidget`, and `SalesRepLeaderboardWidget`.
- [x] Task: Write tests for these widgets' data calculations.
- [x] Task: Implement the Stats section (Total, Monthly, Growth %).
- [x] Task: Implement the "Recent Visits" table (Last 5-10 entries).
- [x] Task: Implement the "Sales Rep Leaderboard" (Visits per subordinate).
- [x] Task: Register widgets in `AdminPanelProvider.php` if needed.
- [x] Task: Conductor - User Manual Verification 'Dashboard Widget Implementation' (Protocol in workflow.md)

## Phase 3: Contextual Company Widget [x]
- [x] Task: Generate `CompanyVisitStatsWidget` and `CompanyRecentVisitsWidget`.
- [x] Task: Write tests for these widgets' data calculations.
- [x] Task: Implement contextual Stats: "Last Visit" and "Total Visits for this Company".
- [x] Task: Implement contextual "Recent Activity" table.
- [x] Task: Add `CompanyVisitStatsWidget` and `CompanyRecentVisitsWidget` to `CompanyResource` view page.
- [x] Task: Conductor - User Manual Verification 'Contextual Company Widget' (Protocol in workflow.md)

## Phase 4: Final Polishing & Verification [ ]
- [ ] Task: Run all tests to ensure no regressions.
- [ ] Task: Manual verification of widget visibility across different roles (SR, ASM, Head).
- [ ] Task: Ensure all code matches project style using `vendor/bin/pint`.
- [ ] Task: Conductor - User Manual Verification 'Final Polishing & Verification' (Protocol in workflow.md)
