# Specification: Add VisitWidget

## Overview
Implement a `VisitWidget` system to provide users with quick insights into sales visit activities. The widget will be deployed in two primary locations: the main Dashboard for high-level oversight and the Company Resource for contextual history.

## Functional Requirements

### 1. Dashboard Widget (Global/Hierarchical)
- **Stats Section:**
    - Total Visits: Total count of visits accessible to the user.
    - Monthly Visits: Visits recorded in the current calendar month.
    - Growth Metric: Percentage increase/decrease compared to the previous month.
- **Recent Activity:**
    - A table or list showing the 5-10 most recent visits (Date, Company, Rep, and Outcome snippet).
- **Sales Rep Breakdown:**
    - A leaderboard/list showing the number of visits performed by each Sales Representative within the user's reporting hierarchy.

### 2. Company Resource Widget (Contextual)
- **Contextual Stats:**
    - Last Visit Date: The date of the most recent interaction with this specific company.
    - Total Company Visits: Historical count of visits for this company.
- **Recent Activity:**
    - A list of recent visits specific to the current company being viewed.

### 3. Data Scoping & Security
- **Hierarchical Access:** Data displayed must strictly adhere to the reporting lines defined in the system.
    - **SR/SPV:** See only their own visits.
    - **ASM/RSM:** See their own visits and those of their subordinates.
    - **Heads/BOD:** See global visit statistics.
- **Tenant/Role Integrity:** Ensure Filament widgets respect the existing RBAC and scoping logic.

## Non-Functional Requirements
- **Performance:** Widget queries must be optimized (using eager loading for relations like `Company` and `User`) to ensure fast page loads.
- **UI Consistency:** Use standard Filament Widget components to maintain the "MMG Healthcare CRM" aesthetic.

## Acceptance Criteria
- [ ] `VisitWidget` is visible on the Filament Dashboard.
- [ ] `VisitWidget` is visible on the Company Resource view page.
- [ ] Dashboard version shows the Sales Rep leaderboard; Company version does not.
- [ ] Data is correctly scoped based on the logged-in user's role and hierarchy.
- [ ] Stats (Total/Monthly/Growth) update correctly based on the `Visit` model data.
- [ ] Recent activity links correctly to the Visit details or Company page.

## Out of Scope
- Visual charts (line/bar charts) are deferred to a future iteration.
- Exporting visit data directly from the widget.
