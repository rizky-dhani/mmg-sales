---
change-id: add_principal_to_products
---

# Tasks: Add Principal Relationship to Products

This document outlines the tasks required to implement the "Add Principal Relationship to Products" proposal.

## Phase 1: Database and Model Changes

- [ ] Create a migration to add `principal_id` (foreign key) to the `products` table.
- [ ] Update `app/Models/Product.php` to:
    - [ ] Add `principal_id` to the `$fillable` array.
    - [ ] Define the `belongsTo` relationship with `Principal`.
- [ ] Update `app/Models/Principal.php` to:
    - [ ] Define the `hasMany` relationship with `Product`.

## Phase 2: Filament Integration

- [ ] Modify `app/Filament/Resources/Products/Schemas/ProductForm.php` to include a `Select` field for `principal_id`.
    - [ ] The select field should fetch principals from the database.
    - [ ] It should be required.
- [ ] Modify `app/Filament/Resources/Products/Schemas/ProductInfolist.php` to display the related `Principal`'s name.

## Phase 3: Testing

- [ ] Create a new feature test `tests/Feature/Filament/Products/ProductPrincipalRelationshipTest.php` to cover:
    - [ ] Ensuring a product can be created with an associated principal via the Filament form.
    - [ ] Ensuring the principal is displayed correctly in the Filament infolist.
    - [ ] Testing the database relationship integrity.

## Phase 4: Code Style and Validation

- [ ] Run `vendor/bin/pint --dirty` to apply code style fixes.
- [ ] Validate OpenSpec with `openspec validate add_principal_to_products --strict`.
