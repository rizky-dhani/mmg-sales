<?php

namespace App\Imports;

use App\Models\Principal;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PrincipalsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (empty($row['name'])) {
                continue;
            }

            Principal::create([
                'name' => $row['name'],
                'initial' => $row['initial'] ?? null,
                'description' => $row['description'] ?? null,
                'is_active' => $row['is_active'] ?? 1,
                'principal_code' => $row['principal_code'] ?? null,
                'principal_acc_code' => $row['principal_acc_code'] ?? null,
            ]);
        }
    }
}
