## MODIFIED Requirements

### Requirement: Company Model Renaming
The `App\Models\Company` model MUST be renamed to `App\Models\Customer` to accurately reflect its role within the business domain.
#### Scenario:
- **Original State**: The application includes an Eloquent model named `App\Models\Company`.
- **New State**: The `App\Models\Company` model MUST be renamed to `App\Models\Customer`.

### Requirement: Filament Resource Renaming
The Filament administration interface for managing company records MUST be updated to use 'Customer' terminology, including directory and file names, to maintain consistency with the renamed model.
#### Scenario:
- **Original State**: The application includes a Filament resource located at `App\Filament\Resources\Companies\CompanyResource.php`.
- **New State**: The `App\Filament\Resources\Companies\CompanyResource.php` MUST be renamed to `App\Filament\Resources\Customers\CustomerResource.php`. All associated files within the `App\Filament\Resources\Companies` directory (e.g., `CompanyForm.php`, `CompaniesTable.php`, `CompanyInfolist.php`, `ListCompanies.php`, `CreateCompany.php`, `EditCompany.php`, `ViewCompany.php`) MUST be renamed to reflect the `Customer` terminology.

### Requirement: Database Table Renaming
The `companies` database table MUST be renamed to `customers` to align with the updated model and resource naming.
#### Scenario:
- **Original State**: The database contains a table named `companies`.
- **New State**: The `companies` database table MUST be renamed to `customers`. A new migration MUST be created to perform this table rename.

### Requirement: Foreign Key Updates
Existing foreign key constraints referencing the `companies` table MUST be updated to point to the new `customers` table and use the `customer_id` column name to ensure data integrity.
#### Scenario:
- **Original State**: Foreign keys in other tables reference `company_id`.
- **New State**: All foreign keys referencing `company_id` MUST be updated to `customer_id`. This includes modifying existing migration files or creating new ones where necessary.

### Requirement: Codebase References Update
All code references throughout the application to the 'Company' model, 'companies' table, or 'company_id' columns MUST be systematically updated to 'Customer', 'customers', and 'customer_id' respectively to prevent runtime errors.
#### Scenario:
- **Original State**: Various files throughout the codebase (e.g., controllers, policies, factories, tests, other models) contain references to `Company` or `companies`.
- **New State**: All references to `Company` (class names, variable names, method names, comments) MUST be updated to `Customer`. All references to the `companies` table or `company_id` column MUST be updated to `customers` or `customer_id`, respectively.

### Requirement: Policy Renaming
The authorization policy for the Company model MUST be renamed and updated to align with the new Customer model.
#### Scenario:
- **Original State**: The application contains a policy named `App\Policies\CompanyPolicy.php`.
- **New State**: The `App\Policies\CompanyPolicy.php` MUST be renamed to `App\Policies\CustomerPolicy.php`, and its internal references updated to `Customer`.

### Requirement: Factory Renaming
The factory responsible for generating test data for the Company model MUST be renamed and updated to reflect the new Customer model.
#### Scenario:
- **Original State**: The application contains a factory named `database\factories\CompanyFactory.php`.
- **New State**: The `database\factories\CompanyFactory.php` MUST be renamed to `database\factories\CustomerFactory.php`, and its internal references updated to `Customer`.