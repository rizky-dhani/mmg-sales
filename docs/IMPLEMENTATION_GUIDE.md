# MMG Healthcare CRM - Implementation Guide

This document tracks the implementation steps for the Laravel 12 + Filament 4 CRM for MMG Healthcare Distribution.

## Project Overview

**Purpose:** Healthcare Distribution CRM to track sales, leads, efficiency calculations, and probability-based sales forecasting.

**Tech Stack:**
- PHP 8.4.15
- Laravel 12
- Filament 4.4.0
- Spatie Permission 6.24.0
- MariaDB Database

---

## Completed Steps

### Step 1.1: Dependencies Installation ✅

**Status:** Complete

**Actions Taken:**
1. Installed `filament/filament` v4.4.0
2. Installed `spatie/laravel-permission` v6.24.0
3. Installed `guzzlehttp/guzzle` (for wilayah.id API)

**Git Commit:** `feat: install filament 4, spatie permission, and configure base dependencies`

---

### Step 1.1b: Database Migrations ✅

**Status:** Complete

**Actions Taken:**
1. Fixed multiple migration ordering issues:
   - Renamed `customers` migration to run before `contacts` (was referencing non-existent table)
   - Renamed `order_items` migration to run after `orders` (was referencing non-existent table)
2. Fixed foreign key constraint naming issue:
   - Changed `group_customer_id` to `customer_group_id` in orders table (constraint name too long for MariaDB)
3. Created migration `add_org_columns_to_users_table` with fields:
   - `department_id` - Foreign key to departments
   - `position_id` - Foreign key to positions
   - `territory_id` - Foreign key to territories
   - `manager_id` - Self-referencing foreign key for reporting hierarchy
4. Successfully ran `php artisan migrate --force`
5. All 24 migrations completed successfully

**Git Commits:**
- `feat: create database structure and clean up seeders`
- `feat: install filament 4, spatie permission, and configure base dependencies`

**Database Migrations Created:**
| Migration | Description |
|-----------|-------------|
| `0001_01_01_000000_create_users_table.php` | Laravel default |
| `0001_01_01_000001_create_cache_table.php` | Laravel default |
| `0001_01_01_000002_create_jobs_table.php` | Laravel default |
| `2026_01_05_045840_create_territories_table.php` | Territory (self-referencing tree) |
| `2026_01_05_045850_create_customers_table.php` | Customers |
| `2026_01_05_045851_create_customer_groups_table.php` | Customer groups |
| `2026_01_05_045852_create_departments_table.php` | Departments |
| `2026_01_05_045852_create_positions_table.php` | Positions |
| `2026_01_05_045853_create_distributors_table.php` | Distributors |
| `2026_01_05_045854_create_principals_table.php` | Principals |
| `2026_01_05_045854_create_sales_types_table.php` | Sales types |
| `2026_01_05_045854_create_segments_table.php` | Segments |
| `2026_01_05_045857_create_contacts_table.php` | Contacts |
| `2026_01_05_045857_create_leads_table.php` | Leads |
| `2026_01_05_045857_create_products_table.php` | Products |
| `2026_01_05_045910_create_items_table.php` | Items table |
| `2026_01_05_045911_create_sub_segments_table.php` | Sub segments |
| `2026_01_05_050531_create_orders_table.php` | Orders |
| `2026_01_05_050532_create_order_items_table.php` | Order items |
| `2026_01_05_094028_create_permission_tables.php` | Spatie permission tables |
| `2026_01_06_021421_add_org_columns_to_users_table.php` | Organizational columns |

**Configuration Files Published:**
- `config/filament.php`
- `config/permission.php`

**Filament Setup:**
- `app/Providers/Filament/AdminPanelProvider.php` - Created

---

### Step 1.2: Create Territory Model and Update Core Models ✅

**Status:** Complete

**Actions Taken:**
1. Deleted old migration: `database/migrations/2026_01_05_045853_create_area_cities_table.php`
2. Created Territory model with migration: `php artisan make:model Territory --migration --no-interaction`
3. Updated Territory migration with fields:
   - `id` - Primary key
   - `name` - Territory name
   - `wilayah_code` - Wilayah.id code (nullable)
   - `type` - Enum: 'region', 'province', 'city'
   - `level` - Integer: 1=region, 2=province, 3=city
   - `parent_id` - Self-referencing foreign key
   - `manager_id` - Foreign key to users
   - `timestamps` - Created_at, updated_at
4. Created Territory model with relationships and methods:
   - `parent()` - BelongsTo Territory
   - `children()` - HasMany Territory
   - `manager()` - BelongsTo User
   - `getAllDescendantIds()` - Recursive method to get all child territory IDs
   - `getAllAncestorIds()` - Recursive method to get all parent territory IDs
5. Created Department model with relationships:
   - `positions()` - HasMany Position
6. Created Position model with relationships:
   - `department()` - BelongsTo Department
   - `users()` - HasMany User
7. Updated User model with:
   - Added `HasRoles` trait from Spatie Permission
   - Implemented `FilamentUser` interface
   - Added `canAccessPanel(Panel $panel): bool` method
   - Added organizational relationships: `department()`, `position()`, `territory()`, `manager()`, `subordinates()`, `managedTerritories()`

**Files Modified/Created:**
- `app/Models/Territory.php` - Created with relationships
- `app/Models/Department.php` - Created with relationships
- `app/Models/Position.php` - Created with relationships
- `app/Models/User.php` - Updated with traits, interface, and relationships
- `database/migrations/2026_01_05_045840_create_territories_table.php` - Territory migration
- `database/migrations/2026_01_06_021421_add_org_columns_to_users_table.php` - Organizational columns

---

### Step 1.2b: All Core Business Logic Models ✅

**Status:** Complete

**Models Created (16 total):**
1. `User.php` - With FilamentUser, HasRoles, and organizational relationships
2. `Department.php` - With positions relationship
3. `Position.php` - With department and users relationships
4. `Territory.php` - Self-referencing tree with recursive methods
5. `Customer.php` - With contacts, orders, leads relationships; SoftDeletes enabled
6. `CustomerGroup.php` - Customer categorization
7. `Contact.php` - Customer contacts; SoftDeletes enabled
8. `Lead.php` - Sales leads; SoftDeletes enabled
9. `Product.php` - Products; SoftDeletes enabled
10. `Principal.php` - Product principals/manufacturers
11. `Distributor.php` - Distribution partners
12. `SalesType.php` - Sales type categorization
13. `Segment.php` - Market segments
14. `SubSegment.php` - Sub-segment categorization
15. `Item.php` - Order items (products in orders)
16. `Order.php` - Sales orders with relationships

**Eloquent Relationships Mapped:**
- Order belongs to Customer, Territory, Item, Principal, Segment, SubSegment, SalesType, User
- Customer has many Contacts, Orders, Leads
- Product belongs to Principal, Segment, SubSegment
- Territory has self-referencing parent/children relationship
- User has organizational relationships (department, position, territory, manager, subordinates)

---

### Step 1.3: Authentication & RBAC (Spatie Permission) ✅

**Status:** Complete

**Actions Taken:**
1. Created `RolesAndPermissionsSeeder.php`
2. Generated permissions for all models with CRUD actions

**Permissions Generated:**
```
Models: user, department, position, territory, customer, contact, lead, product, order
Actions: view, view_any, create, update, delete, restore, force_delete
```

**Roles Created (9 total):**
1. **SuperAdmin** - All permissions (via gate/policy)
2. **Head** - All permissions
3. **ProductManager** - Product/lead management
4. **JrProductManager** - Junior product management
5. **ProductExecutive** - Product execution
6. **RegionalSalesManager** - Regional sales oversight
7. **AreaSalesManager** - Area sales management
8. **Supervisor** - Team supervision
9. **SalesRepresentative** - Sales activities

**Panel Access:**
- `User::canAccessPanel()` authorized based on Spatie roles

**File Created:**
- `database/seeders/RolesAndPermissionsSeeder.php`

---

### Step 1.4: Filament 4 Admin Implementation ✅

**Status:** Complete

**Actions Taken:**
1. Created Filament resources for all 16 models using Filament 4 architecture
2. Organized into Schemas and Tables directories
3. Customized schemas with proper Select relationships
4. Set correct recordTitleAttribute for non-standard models

**Filament Resources Created (21 total):**

| Resource | Path |
|----------|------|
| UserResource | `app/Filament/Resources/Users/UserResource.php` |
| DepartmentResource | `app/Filament/Resources/Departments/DepartmentResource.php` |
| PositionResource | `app/Filament/Resources/Positions/PositionResource.php` |
| TerritoryResource | `app/Filament/Resources/Territories/TerritoryResource.php` |
| CustomerResource | `app/Filament/Resources/Customers/CustomerResource.php` |
| ContactResource | `app/Filament/Resources/Contacts/ContactResource.php` |
| LeadResource | `app/Filament/Resources/Leads/LeadResource.php` |
| ProductResource | `app/Filament/Resources/Products/ProductResource.php` |
| OrderResource | `app/Filament/Resources/Orders/OrderResource.php` |
| CustomerGroupResource | `app/Filament/Resources/CustomerGroups/CustomerGroupResource.php` |
| DistributorResource | `app/Filament/Resources/Distributors/DistributorResource.php` |
| PrincipalResource | `app/Filament/Resources/Principals/PrincipalResource.php` |
| SalesTypeResource | `app/Filament/Resources/SalesTypes/SalesTypeResource.php` |
| SegmentResource | `app/Filament/Resources/Segments/SegmentResource.php` |
| SubSegmentResource | `app/Filament/Resources/SubSegments/SubSegmentResource.php` |
| ItemResource | `app/Filament/Resources/Items/ItemResource.php` |

**Filament 4 Architecture:**
- Schemas: Separate form schemas (e.g., `OrderForm.php`, `UserForm.php`)
- Tables: Separate table configurations (e.g., `OrdersTable.php`, `UsersTable.php`)
- Pages: List, Create, Edit pages for each resource

**Customizations:**
- UserResource: Role assignment (multi-select), organizational dropdowns
- OrderResource: Proper Select relationships for customers, items, segments, territories
- recordTitleAttribute: Searches by `order_number` for Orders, `facility_name` for Customers

---

## Organizational Structure to Implement

### 9 Roles
1. SuperAdmin (all access)
2. Head (all access)
3. ProductManager
4. JrProductManager
5. ProductExecutive
6. RegionalSalesManager
7. AreaSalesManager
8. Supervisor
9. SalesRepresentative

### 2 Departments
- PROD (Product Department)
- SALES (Sales Department)

### Territory Hierarchy
- Level 1: Region (Java Region)
- Level 2: Province (31, 32, 33, 34, 35, 36)
- Level 3: City (all cities in those provinces)

### 8 Initial Users
| Name | Role | Territories |
|------|------|-------------|
| Dr. Ahmad | Head | All territories |
| Budi | Product Manager | All territories |
| Citra | Jr Product Manager | All territories |
| Dian | Product Executive | All territories |
| Eko | Regional Sales Manager | Java Region |
| Fajar | Area Sales Manager | West Java (32) |
| Gilang | Supervisor | Bandung city |
| Hendra | Sales Representative | Bandung city |

### Reporting Hierarchy
- All product managers report to Head (no explicit manager)
- ASM reports to RSM
- SPV reports to ASM
- SR reports to SPV

---

## Known Technical Issues Resolved

### Issue 1: Migration Ordering
**Problem:** Contacts table tried to reference customers before customers table was created.

**Solution:** Renamed migration to ensure customers runs first.

### Issue 2: Foreign Key Naming
**Problem:** MariaDB constraint name `orders_group_customer_id_foreign` exceeded 64 character limit.

**Solution:** Changed to `orders_customer_group_id_foreign`.

### Issue 3: Duplicate Migrations
**Problem:** Had multiple customer table migrations.

**Solution:** Removed duplicates.

### Issue 4: Seeder Conflict
**Problem:** DatabaseSeeder had test user that conflicted with unique constraint.

**Solution:** Cleaned up seeder.

### Issue 5: FilamentUser Interface Method Name
**Problem:** Used wrong method name `canAccessFilament()` instead of `canAccessPanel()`.

**Solution:** Updated to use correct `canAccessPanel(Panel $panel): bool` method signature.

---

## Key User Preferences & Constraints

- **Database:** Already configured (MariaDB)
- **Territory Focus:** Java region only (6 provinces)
- **User Password:** All users use `Mmg2025!`
- **Branch:** Working on `main` branch
- **Git Commit Strategy:** Clear messages after each step
- **Order Number Format:** `MMG-ORD-YYYY-XXXXXXXX`
- **Lead Status Changes:** Free for Sales Reps (no approval needed)
- **Reporting:** On-demand PDF & Excel exports
- **Mobile Access:** Priority postponed for later
- **Breakdown by:** Product category, territory, and customer type

---

## Expected Database After Step 1.7

### Tables
- `users`, `permissions`, `roles`, `model_has_permissions`, `model_has_roles`
- `departments`, `positions`, `territories`
- `customers`, `contacts`, `customer_groups`
- `products`, `principals`
- `orders`, `order_items`
- `segments`, `sub_segments`, `sales_types`, `distributors`
- `leads`

### Seeded Data
- Users: 0 (pending - Step 1.5)
- Roles: 9 (done via RolesAndPermissionsSeeder)

---

## Commands Reference

```bash
# Create model with migration
php artisan make:model Territory --migration --no-interaction

# Create model without migration
php artisan make:model Department --no-interaction
php artisan make:model Position --no-interaction

# Run migrations
php artisan migrate --force

# Fresh migrate with seeding
php artisan migrate:fresh --seed

# Format code with Pint
vendor/bin/pint --dirty
```

---

## Last Session Notes

**Current Status:** Step 1.4 (Filament 4 Admin Implementation) COMPLETE

**What Was Actually Done (Previous Sessions):**
1. ✅ Step 1.1: Dependencies Installation
2. ✅ Step 1.1b: Database Migrations (24 total)
3. ✅ Step 1.2: Territory Model and Update Core Models
4. ✅ Step 1.2b: All 16 Core Business Logic Models
5. ✅ Step 1.3: Authentication & RBAC (Spatie Permission) with 9 roles
6. ✅ Step 1.4: Filament 4 Admin Implementation with 21 resources

**Pending Tasks (Next Steps):**
1. Step 1.5: DepartmentSeeder, PositionSeeder, UserSeeder
2. Step 1.6: Configure Filament Panel branding
3. Step 1.7: Wilayah.id API Service
4. Step 1.8: Territory Import Command

**Quick Reference for Next AI Session:**
- Run `php artisan db:seed --class=RolesAndPermissionsSeeder` first to seed roles
- Then run `php artisan db:seed` to run all seeders
- All models are created in `app/Models/`
- All Filament resources are in `app/Filament/Resources/`
- User model has `canAccessPanel()` method checking Spatie roles
