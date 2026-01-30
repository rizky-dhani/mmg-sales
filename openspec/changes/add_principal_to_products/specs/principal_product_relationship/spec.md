---
change-id: add_principal_to_products
---

# Spec Delta: Principal-Product Relationship

## ADDED Requirements

### Requirement: Principal-Product Relationship

A `Product` SHALL be optionally associated with an existing `Principal`.

#### Scenario: Products can be associated with a Principal

*   **Given**: A `Principal` exists in the system.
*   **When**: A new `Product` is created.
*   **Then**: The `Product` can be optionally associated with an existing `Principal` via its `principal_id`.
*   **And**: The `principal_id` column must exist on the `products` table, be of type `BIGINT UNSIGNED`, and serve as a foreign key to the `principals` table, with `ON DELETE SET NULL` behavior.
*   **And**: The `Principal` model must define a `hasMany` relationship with `Product`.
*   **And**: The `Product` model must define a `belongsTo` relationship with `Principal`.

### Requirement: ProductForm allows selection of a Principal

The `ProductForm` in Filament MUST allow users to select an associated `Principal`.

#### Scenario: ProductForm allows selection of a Principal

*   **Given**: The user is accessing the `ProductForm` in Filament to create or edit a `Product`.
*   **When**: The form is displayed.
*   **Then**: A `Select` field labeled "Principal" should be available.
*   **And**: This field should populate with a list of available `Principals` (displaying their `name`).
*   **And**: The selected `Principal` should be correctly associated with the `Product` upon form submission.

### Requirement: ProductInfolist displays associated Principal

The name of the associated `Principal` MUST be visible in the `ProductInfolist`.

#### Scenario: ProductInfolist displays associated Principal

*   **Given**: A `Product` is associated with a `Principal`.
*   **When**: The `ProductInfolist` is displayed for that `Product`.
*   **Then**: The name of the associated `Principal` should be visible as a `TextEntry` labeled "Principal".