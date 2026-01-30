---
change-id: add_principal_to_products
---

# Design: Add Principal Relationship to Products

## Architectural Considerations

The `Principal` and `Product` models currently exist independently. The proposed change introduces a direct one-to-many relationship between `Principal` and `Product`, where a `Principal` can have many `Products`, and each `Product` belongs to one `Principal`. This is a standard relational database pattern and aligns with Laravel's Eloquent ORM capabilities.

## Database Schema Changes

A new column `principal_id` will be added to the `products` table. This column will be an unsigned big integer and will be a foreign key referencing the `id` column of the `principals` table. It will be nullable to allow for existing products that may not immediately have a principal assigned, or if a principal is not strictly required for all products. An index will be added to `principal_id` for performance.

```sql
ALTER TABLE products
ADD COLUMN principal_id BIGINT UNSIGNED NULL,
ADD CONSTRAINT fk_products_principal_id FOREIGN KEY (principal_id) REFERENCES principals (id) ON DELETE SET NULL;
```
*(Note: `ON DELETE SET NULL` assumes that if a Principal is deleted, its associated products should not be deleted, but rather have their `principal_id` set to null. This should be confirmed with the user or product owner.)*

## Model Layer

### `App\Models\Product`

A `belongsTo` relationship will be defined:

```php
public function principal(): BelongsTo
{
    return $this->belongsTo(Principal::class);
}
```

The `principal_id` will be added to the `$fillable` property to allow mass assignment.

### `App\Models\Principal`

A `hasMany` relationship will be defined:

```php
public function products(): HasMany
{
    return $this->hasMany(Product::class);
}
```

## Filament Integration

### `ProductForm` (Filament Schema)

A `Select` component will be added to the `ProductForm` to allow users to select a `Principal`. This will leverage Filament's relationship handling capabilities.

```php
Select::make('principal_id')
    ->relationship('principal', 'name') // 'principal' is the relationship method in Product model, 'name' is the field to display
    ->searchable()
    ->preload()
    ->createOptionForm([
        // ... fields for creating a new principal if desired ...
    ])
    ->editOptionForm([
        // ... fields for editing an existing principal if desired ...
    ])
    ->required(), // Or nullable, depending on product owner's decision
```

### `ProductInfolist` (Filament Schema)

A `TextEntry` component will be added to the `ProductInfolist` to display the name of the associated `Principal`.

```php
TextEntry::make('principal.name') // Accessing the name through the 'principal' relationship
    ->label('Principal'),
```

## Testing Strategy

A dedicated feature test will be created to verify:
1.  The `principal_id` column is added to the `products` table.
2.  The `Product` and `Principal` models correctly define and interact via their relationships.
3.  A `Product` can be successfully created and updated with a `Principal` via the Filament form.
4.  The `Principal`'s name is correctly displayed in the `ProductInfolist`.
5.  Edge cases, such as deleting a `Principal` (if `ON DELETE SET NULL` is chosen) and its effect on `Product` records.
