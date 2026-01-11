# Spec: Implement Board of Director Role and Global Oversight Access

## Overview
This track focuses on establishing the "Board of Director" (BOD) role within the MMG Healthcare CRM. The BOD role is designed for strategic oversight, requiring read-only access to all company data across the Java region without the ability to create, update, or delete records.

## Goals
- Formally define the 'Board of Director' role in the system.
- Ensure the BOD role has `view` and `view_any` permissions for all relevant models.
- Position the BOD role at the top of the organizational hierarchy (Level 0, parent of 'Head').
- Restrict BOD access to be strictly read-only through Laravel Policies.

## Technical Details
- **Role Name:** `Board of Director`
- **Department:** `MGMT` (Management)
- **Position Level:** `0`
- **Permissions:** `view_any_*`, `view_*` for all models.
- **Policies:** `BasePolicy` and specific model policies must strictly enforce read-only access for this role.

## Acceptance Criteria
- A user with the 'Board of Director' role can log in to the Filament panel.
- The BOD user can see all resources (Users, Customers, Leads, Orders, etc.).
- The BOD user *cannot* see "Create" buttons or edit/delete actions for any resource.
- The organizational hierarchy correctly lists BOD above the Head of Department.
- Unit tests verify that BOD users are denied `create`, `update`, and `delete` permissions.
