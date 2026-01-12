# Track Spec: Segment and Sub Segment Management

## Overview
This track handles both the visibility and data population for `Segment` and `SubSegment` resources in the Filament admin panel. It enables navigation items exclusively for Super Admins and populates the database with standardized data using a seeder.

## Functional Requirements
### 1. Resource Visibility (Super Admin Only)
- Update `App\Filament\Resources\Segments\SegmentResource` to show in navigation only for Super Admins.
- Update `App\Filament\Resources\SubSegments\SubSegmentResource` to show in navigation only for Super Admins.
- Verify `SegmentPolicy` and `SubSegmentPolicy` restrict CRUD actions to Super Admins.

### 2. Data Population
- Create `database/seeders/SegmentSeeder.php` to populate the following segments and their respective sub-segments:
    - PRIVATE LAB - CD
    - INDUSTRY (PHARMA & VACCINES, CHEMICAL, LIVESTOCK & FEED, Agriculture Industry)
    - UNIVERSITY (UNIV-Negeri, UNIV-Swasta)
    - HOSPITAL (HOSPITAL, UNIV-Negeri)
    - SUPPLIER (SUPPLIER)
    - GOVERNMENT LAB - NCD
    - GOVERNMENT LAB - CD
    - PRIVATE LAB - NCD (PRIVATE LAB-NCD, BLOOD BANK)
    - DINKES/PKM (DINKES/MOH, PUSKESMAS)
    - KLINIK

### 3. Seeder Implementation
- Use `updateOrCreate` to ensure idempotency.
- Automatically generate unique `code` values for segments and sub-segments.
- Register the seeder in `DatabaseSeeder.php`.

## Acceptance Criteria
- [ ] Only Super Admins see "Segments" and "Sub Segments" in the CRM navigation group.
- [ ] Running the seeder correctly populates all 10 segments and their 16 sub-segments.
- [ ] Hierarchical relationships (Segment -> SubSegment) are correctly established.
- [ ] No duplicate records are created on multiple runs.

## Out of Scope
- UI layout modifications.
- Adding data for other entities.