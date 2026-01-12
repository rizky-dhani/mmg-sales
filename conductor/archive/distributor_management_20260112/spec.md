# Track Spec: Distributor Management

## Overview
This track enables the `Distributor` resource in the Filament admin panel exclusively for Super Admins and populates the database with core distributor data (MMG and MJG) via a seeder.

## Functional Requirements
### 1. Resource Visibility (Super Admin Only)
- Update `App\Filament\Resources\Distributors\DistributorResource` to show in navigation only for Super Admins.
- Verify `DistributorPolicy` restricts access to Super Admins.

### 2. Data Population
- Create `database/seeders/DistributorSeeder.php` to populate the following distributors:
    - **MMG** (Code: MMG)
    - **MJG** (Code: MJG)
- The seeder should use `updateOrCreate` to be idempotent.

### 3. Resource Integration
- Register the seeder in `DatabaseSeeder.php`.

## Acceptance Criteria
- [ ] Only Super Admins see "Distributors" in the "Product & Inventory" navigation group.
- [ ] Direct access to Distributor resource URLs returns 403 for non-Super Admins.
- [ ] Running the seeder populates exactly two distributors: MMG and MJG.
- [ ] Unique `code` values (MMG, MJG) are populated correctly.

## Out of Scope
- Modifying the fields or layout of Distributor forms and tables.
- Adding contact details or addresses beyond basic seeder requirements unless specified.
