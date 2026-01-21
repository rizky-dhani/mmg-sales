# Design: Refactor Company to Customer

## Overview
This design document details the renaming of the `Company` entity to `Customer` across the Laravel application. This includes model names, Filament resources, database tables, and all associated code references to ensure consistent terminology that reflects the domain's understanding that a 'Company' is always a 'Customer'.

## Renaming Strategy

### 1. Model
- The `App\Models\Company` model will be renamed to `App\Models\Customer`.

### 2. Filament Resource
- The `App\Filament\Resources\Companies` directory will be renamed to `App\Filament\Resources\Customers`.
- All files within this directory, including `CompanyResource.php`, `CompanyForm.php`, `CompaniesTable.php`, `CompanyInfolist.php`, `ListCompanies.php`, `CreateCompany.php`, `EditCompany.php`, and `ViewCompany.php` will be renamed to use `Customer` instead of `Company`.
- The `VisitsRelationManager` within the `CompanyResource` will be updated to reflect the new `CustomerResource`.

### 3. Database Table
- The `companies` database table will be renamed to `customers`. A new migration will be created for this purpose.
- Any foreign keys referencing `company_id` will be updated to `customer_id` where appropriate, along with updating relevant migration files.

### 4. Code References
- All occurrences of `Company` (class names, variable names, method names, comments, etc.) will be updated to `Customer` throughout the codebase, particularly in files related to the Filament resources, models, policies, factories, and tests.
- References to the `companies` table or `company_id` column in migrations, seeders, factories, and other parts of the application will be updated to `customers` or `customer_id` respectively.

### 5. Policies
- `App\Policies\CompanyPolicy.php` will be renamed to `App\Policies\CustomerPolicy.php` and its contents updated accordingly.

### 6. Factories and Seeders
- `database\factories\CompanyFactory.php` will be renamed to `database\factories\CustomerFactory.php`.
- Seeders that create or interact with `Company` instances will be updated to `Customer`.

## Impact Assessment

- **Database**: A migration will be created to rename the `companies` table and update foreign key constraints. This will be a critical step requiring careful execution.
- **Filament**: All Filament-related files for the `Company` resource will be renamed and updated. This includes the resource class, pages, forms, tables, infolists, and relation managers. Navigation entries will also be updated.
- **Eloquent Relationships**: All models that have relationships with `Company` will need their relationship definitions updated to `Customer`.
- **Policies**: The `CompanyPolicy` will be renamed and updated to `CustomerPolicy`.
- **Tests**: Any existing tests that interact with `Company` or its resource will need to be updated to `Customer`.
- **Other Files**: Any other files that explicitly reference the `Company` model or `companies` table will need to be updated.

This comprehensive renaming will ensure consistency and clarity, reflecting the precise role of the entity within the application.
