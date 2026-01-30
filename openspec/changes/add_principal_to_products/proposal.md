---
change-id: add_principal_to_products
---

# Proposal: Add Principal Relationship to Products

This proposal outlines the changes required to establish a relationship between the `Principal` and `Product` models, and subsequently update the Filament `ProductForm` and `ProductInfolist` to reflect this relationship.

## Motivation

Currently, the `Product` model does not have a direct association with a `Principal`. Many products are supplied by a specific principal, and establishing this relationship will allow for better organization, filtering, and reporting of products based on their principal. This will enhance the data integrity and usability of the application, especially within the Filament admin panel.

## Proposed Changes

1.  **Database Migration**: Add a `principal_id` column to the `products` table to establish a one-to-many relationship, where a `Principal` can have many `Products`.
2.  **Model Updates**:
    *   Add a `belongsTo` relationship to the `Product` model to link it to the `Principal` model.
    *   Add a `hasMany` relationship to the `Principal` model to link it to multiple `Product` models.
    *   Update the `$fillable` array in the `Product` model to include `principal_id`.
3.  **Filament `ProductForm` Update**: Integrate a `Select` field into the `ProductForm` that allows users to choose an associated `Principal` when creating or editing a product.
4.  **Filament `ProductInfolist` Update**: Display the name of the associated `Principal` in the `ProductInfolist` for better contextual information.
5.  **Tests**: Add feature tests to ensure the new relationship is correctly established and the Filament forms/infolists function as expected.

## Impact

*   **Database**: Addition of a new column (`principal_id`) to the `products` table.
*   **Models**: `Principal` and `Product` models will have new relationship methods.
*   **Filament Admin Panel**: Enhanced product management capabilities with principal association.
*   **Data Integrity**: Improved data integrity by linking products to their respective principals.

## Alternatives Considered

None. A direct one-to-many relationship is the most straightforward and appropriate solution for this requirement.
