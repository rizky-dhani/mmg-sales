<?php

namespace App\Imports;

use App\Models\Principal;
use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (empty($row['name'])) {
                continue;
            }

            Product::create([
                'name' => $row['name'],
                'category' => $row['category'] ?? null,
                'description' => $row['description'] ?? null,
                'unit_price' => $row['unit_price'] ?? 0,
                'unit_of_measure' => $row['unit_of_measure'] ?? 'pcs',
                'is_active' => $row['is_active'] ?? 1,
                'principal_id' => $this->resolvePrincipalId($row['principal_id'] ?? null),
            ]);
        }
    }

    private function resolvePrincipalId(mixed $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $principal = Principal::where('name', $value)->first();

        return $principal?->id;
    }
}
