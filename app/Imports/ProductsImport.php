<?php

namespace App\Imports;

use App\Models\Principal;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

            $principalId = $this->resolvePrincipalId($row['principal_id'] ?? null);

            Product::updateOrCreate(
                [
                    'name' => $row['name'],
                    'principal_id' => $principalId,
                ],
                [
                    'name' => $row['name'],
                    'category' => $row['category'] ?? null,
                    'description' => $row['description'] ?? null,
                    'internal_code' => $row['internal_code'] ?? null,
                    'unit_price' => $row['unit_price'] ?? 0,
                    'ecatalog_price' => $row['ecatalog_price'] ?? null,
                    'unit_of_measure' => $row['unit_of_measure'] ?? 'pcs',
                    'is_active' => $row['is_active'] ?? 1,
                    'principal_id' => $principalId,
                ]
            );
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

        return Principal::firstOrCreate(
            ['name' => $value],
            ['initial' => $this->generateInitial($value)]
        )->id;
    }

    private function generateInitial(string $name): string
    {
        $base = Str::upper(Str::substr($name, 0, 3));
        $initial = $base;
        $counter = 1;

        while (Principal::where('initial', $initial)->exists()) {
            $initial = $base.$counter;
            $counter++;
        }

        return $initial;
    }
}
