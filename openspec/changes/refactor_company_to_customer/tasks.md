# Tasks: Refactor Company to Customer

This document outlines the ordered list of tasks required to refactor the 'Company' entity to 'Customer' throughout the application.

1.  **Rename Model File and Class:**
    *   Rename `app/Models/Company.php` to `app/Models/Customer.php`.
    *   Update the class name inside `app/Models/Customer.php` from `Company` to `Customer`.

2.  **Rename Filament Resource Directory and Files:**
    *   Rename `app/Filament/Resources/Companies` directory to `app/Filament/Resources/Customers`.
    *   Rename `app/Filament/Resources/Customers/CompanyResource.php` to `app/Filament/Resources/Customers/CustomerResource.php`.
    *   Update the class name inside `app/Filament/Resources/Customers/CustomerResource.php` from `CompanyResource` to `CustomerResource`.
    *   Rename `app/Filament/Resources/Customers/Schemas/CompanyForm.php` to `app/Filament/Resources/Customers/Schemas/CustomerForm.php`.
    *   Update the class name inside `app/Filament/Resources/Customers/Schemas/CustomerForm.php` from `CompanyForm` to `CustomerForm`.
    *   Rename `app/Filament/Resources/Customers/Tables/CompaniesTable.php` to `app/Filament/Resources/Customers/Tables/CustomersTable.php`.
    *   Update the class name inside `app/Filament/Resources/Customers/Tables/CustomersTable.php` from `CompaniesTable` to `CustomersTable`.
    *   Rename `app/Filament/Resources/Customers/Schemas/CompanyInfolist.php` to `app/Filament/Resources/Customers/Schemas/CustomerInfolist.php`.
    *   Update the class name inside `app/Filament/Resources/Customers/Schemas/CustomerInfolist.php` from `CompanyInfolist` to `CustomerInfolist`.
    *   Rename `app/Filament/Resources/Customers/Pages/ListCompanies.php` to `app/Filament/Resources/Customers/Pages/ListCustomers.php`.
    *   Update the class name inside `app/Filament/Resources/Customers/Pages/ListCustomers.php` from `ListCompanies` to `ListCustomers`.
    *   Rename `app/Filament/Resources/Customers/Pages/CreateCompany.php` to `app/Filament/Resources/Customers/Pages/CreateCustomer.php`.
    *   Update the class name inside `app/Filament/Resources/Customers/Pages/CreateCustomer.php` from `CreateCompany` to `CreateCustomer`.
    *   Rename `app/Filament/Resources/Customers/Pages/EditCompany.php` to `app/Filament/Resources/Customers/Pages/EditCustomer.php`.
    *   Update the class name inside `app/Filament/Resources/Customers/Pages/EditCustomer.php` from `EditCompany` to `EditCustomer`.
    *   Rename `app/Filament/Resources/Customers/Pages/ViewCompany.php` to `app/Filament/Resources/Customers/Pages/ViewCustomer.php`.
    *   Update the class name inside `app/Filament/Resources/Customers/Pages/ViewCustomer.php` from `ViewCompany` to `ViewCustomer`.
    *   Update all internal references within the Filament resource files from `Company` to `Customer` (e.g., model references, schema calls).

3.  **Rename Policy File and Class:**
    *   Rename `app/Policies/CompanyPolicy.php` to `app/Policies/CustomerPolicy.php`.
    *   Update the class name inside `app/Policies/CustomerPolicy.php` from `CompanyPolicy` to `CustomerPolicy`.
    *   Update any references to `Company` within the policy to `Customer`.

4.  **Rename Factory File and Class:**
    *   Rename `database/factories/CompanyFactory.php` to `database/factories/CustomerFactory.php`.
    *   Update the class name inside `database/factories/CustomerFactory.php` from `CompanyFactory` to `CustomerFactory`.
    *   Update any references to `Company` within the factory to `Customer`.

5.  **Create Database Migration for Table and Column Renames:**
    *   Generate a new migration file: `php artisan make:migration rename_companies_to_customers_and_update_foreign_keys --table=companies`.
    *   In the generated migration file, implement the schema changes to:
        *   Rename the `companies` table to `customers`.
        *   Rename `company_id` columns to `customer_id` in all relevant tables (`contacts`, `leads`, `orders`, `visits`).
        *   Update foreign key constraints accordingly.

6.  **Update Other Code References:**
    *   Perform a global search and replace for "Company" to "Customer" (case-sensitive where appropriate) in PHP files, ensuring only relevant instances are changed (e.g., not changing "company" in natural language comments unless it refers to the model).
    *   Perform a global search and replace for "companies" to "customers" and "company_id" to "customer_id" in PHP and database-related files (e.g., seeders, other models' relationships).

7.  **Update Config Files:**
    *   Check `config/auth.php` or any other configuration files that might explicitly reference the `Company` model or `companies` table.

8.  **Run Migrations:**
    *   Execute `php artisan migrate` to apply the database changes.

9.  **Run Tests:**
    *   Update existing tests to reflect the new `Customer` model and resource.
    *   Run `php artisan test` to ensure all tests pass.

10. **Run Pint:**
    *   Run `vendor/bin/pint --dirty` to fix any formatting issues.

11. **Verify Application Functionality:**
    *   Manually test the Filament UI for customer management to ensure all features are working correctly.
    *   Verify any features that interact with the `Customer` model (formerly `Company`).