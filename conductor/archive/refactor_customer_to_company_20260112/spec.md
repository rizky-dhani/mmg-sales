# Track Spec: Full Refactor from Customer to Company

## Overview
This track performs a comprehensive system-wide refactor to rename the `Customer` entity to `Company` and `CustomerGroup` to `CompanyGroup`. This includes renaming database tables, Eloquent models, foreign keys, permissions, and all Filament UI components to ensure absolute terminology consistency.

## Refactor Scope
### 1. Database Schema
- Rename table `customers` to `companies`.
- Rename table `customer_groups` to `company_groups`.
- **Rename Foreign Keys in all tables:**
    - `orders`: `end_customer_id` -> `end_company_id`, `original_customer_id` -> `original_company_id`, `customer_group_id` -> `company_group_id`.
    - `leads`: `customer_id` -> `company_id`.
    - `contacts`: `customer_id` -> `company_id`.
    - `users`: `customer_id` -> `company_id` (if exists).
    - `company_groups` (formerly `customer_groups`): update any internal references.

### 2. Eloquent Models & Logic
- Rename `App\Models\Customer` to `App\Models\Company`.
- Rename `App\Models\CustomerGroup` to `App\Models\CompanyGroup`.
- Update all relationships in `Order`, `Lead`, `Contact`, `User`, etc.
- Update all namespaces, imports, and variable names in the codebase.

### 3. Filament UI
- Rename `App\Filament\Resources\Customers` directory to `App\Filament\Resources\Companies`.
- Rename `App\Filament\Resources\CustomerGroups` directory to `App\Filament\Resources\CompanyGroups`.
- Update all Resource classes, Schemas, Tables, and Pages to use the `Company` terminology.
- Enable the navigation item for the new `CompanyResource` in the "CRM" group.

### 4. RBAC & Security
- Rename all permissions from `*_customer` to `*_company`.
- Rename all permissions from `*_customergroup` to `*_companygroup`.
- Update `CompanyPolicy` and `CompanyGroupPolicy`.

## Acceptance Criteria
- [ ] The database contains `companies` and `company_groups` tables with correctly named foreign keys.
- [ ] The "Companies" and "Company Groups" navigation items are visible in the Filament sidebar.
- [ ] All existing data is preserved and accessible via the new models.
- [ ] CRUD operations work perfectly for both new resources.
- [ ] Related resources (Orders, Leads, Contacts) correctly reference and display Company data.

## Out of Scope
- Changing the actual data fields or business logic (pure terminology refactor).