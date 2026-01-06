# MMG Healthcare CRM - Next Steps

This document outlines the remaining implementation tasks for the MMG Healthcare CRM.

## Current Status

**Completed:**
- ✅ Dependencies installation (Filament 4, Spatie Permission)
- ✅ Database migrations (24 tables including org structure)
- ✅ All 16 models with Eloquent relationships + SoftDeletes
- ✅ Authentication & RBAC (9 roles, 63 permissions)
- ✅ Filament 4 admin resources (21 resources with Schemas/Tables architecture)

**Pending:**
- ⏳ Seeders (Department, Position, User)
- ⏳ Filament panel branding configuration
- ⏳ Wilayah.id API service
- ⏳ Territory import command

---

## Step 1.5: Seeders (Priority)

### Task 1.5.1: DepartmentSeeder
**File:** `database/seeders/DepartmentSeeder.php`

```php
// Create 2 departments
['code' => 'PROD', 'name' => 'Product Department'],
['code' => 'SALES', 'name' => 'Sales Department']
```

### Task 1.5.2: PositionSeeder
**File:** `database/seeders/PositionSeeder.php`

```php
// PROD Department (hierarchy_level 1-4)
['name' => 'Head', 'code' => 'HEAD', 'department_id' => 1, 'hierarchy_level' => 1]
['name' => 'Product Manager', 'code' => 'PM', 'department_id' => 1, 'hierarchy_level' => 2]
['name' => 'Jr Product Manager', 'code' => 'JPM', 'department_id' => 1, 'hierarchy_level' => 3]
['name' => 'Product Executive', 'code' => 'PE', 'department_id' => 1, 'hierarchy_level' => 4]

// SALES Department (hierarchy_level 1-4)
['name' => 'Regional Sales Manager', 'code' => 'RSM', 'department_id' => 2, 'hierarchy_level' => 1]
['name' => 'Area Sales Manager', 'code' => 'ASM', 'department_id' => 2, 'hierarchy_level' => 2]
['name' => 'Supervisor', 'code' => 'SPV', 'department_id' => 2, 'hierarchy_level' => 3]
['name' => 'Sales Representative', 'code' => 'SR', 'department_id' => 2, 'hierarchy_level' => 4]
```

### Task 1.5.3: UserSeeder
**File:** `database/seeders/UserSeeder.php`

**All users password:** `Mmg2025!` (hashed)

| Name | Email | Role | Position | Department | Territory | Manager |
|------|-------|------|----------|------------|-----------|---------|
| Dr. Ahmad | ahmad@mmg.id | Head | Head | PROD | All (null) | null |
| Budi | budi@mmg.id | ProductManager | PM | PROD | All (null) | Ahmad |
| Citra | citra@mmg.id | JrProductManager | JPM | PROD | All (null) | Budi |
| Dian | dian@mmg.id | ProductExecutive | PE | PROD | All (null) | Budi |
| Eko | eko@mmg.id | RegionalSalesManager | RSM | SALES | Java Region | Ahmad |
| Fajar | fajar@mmg.id | AreaSalesManager | ASM | SALES | West Java (32) | Eko |
| Gilang | gilang@mmg.id | Supervisor | SPV | SALES | Bandung city | Fajar |
| Hendra | hendra@mmg.id | SalesRepresentative | SR | SALES | Bandung city | Gilang |

### Task 1.5.4: Update DatabaseSeeder
**File:** `database/seeders/DatabaseSeeder.php`

```php
$this->call([
    RolesAndPermissionsSeeder::class,  // Already exists
    DepartmentSeeder::class,            // NEW
    PositionSeeder::class,              // NEW
    UserSeeder::class,                  // NEW
]);
```

---

## Step 1.6: Configure Filament Panel Branding

**File:** `config/filament.php`

```php
return [
    'default' => 'admin',
    'panels' => [
        'admin' => [
            'path' => 'admin',
            'display_name' => 'MMG Healthcare CRM',
            'logo' => public_path('mmg-logo.png'),  // Add logo to public/
            'colors' => [
                'primary' => '#0891b2',  // Cyan-600
            ],
        ],
    ],
];
```

**Add to `public/`:**
- `mmg-logo.png` - Company logo

---

## Step 1.7: Wilayah.id API Service

**File:** `app/Services/WilayahApiService.php`

```php
class WilayahApiService
{
    protected string $baseUrl = 'https://wilayah.id/api';

    public function getProvinces(): array;
    public function getRegencies(int $provinceCode): array;
    public function getDistricts(int $cityCode): array;
}
```

**Dependencies:** `guzzlehttp/guzzle` already installed

---

## Step 1.8: Territory Import Command

**File:** `app/Console/Commands/ImportTerritoriesCommand.php`

**Requirements:**
1. Create Java Region (level 1, parent_id = null)
2. Import 6 provinces (level 2):
   - 31: DKI Jakarta
   - 32: West Java
   - 33: Central Java
   - 34: Yogyakarta
   - 35: East Java
   - 36: Banten
3. Import all cities for each province (level 3)
4. Use WilayahApiService to fetch data

**Execute command:**
```bash
php artisan territories:import java
```

---

## Quick Start Commands

```bash
# Seed roles first
php artisan db:seed --class=RolesAndPermissionsSeeder

# Seed all data
php artisan db:seed

# Import territories (after Step 1.7-1.8)
php artisan territories:import java

# Run tests
php artisan test

# Format code
vendor/bin/pint --dirty
```

---

## File Locations Reference

| Type | Location |
|------|----------|
| Models | `app/Models/` |
| Filament Resources | `app/Filament/Resources/` |
| Seeders | `database/seeders/` |
| Migrations | `database/migrations/` |
| Config | `config/filament.php`, `config/permission.php` |

---

## Important Notes

1. **Password:** All seeded users use `Mmg2025!`
2. **Territory Focus:** Java region only (6 provinces)
3. **Roles:** 9 roles already seeded via `RolesAndPermissionsSeeder`
4. **Order Number Format:** `MMG-ORD-YYYY-XXXXXXXX`
5. **Lead Status:** Free changes for Sales Reps (no approval flow)
