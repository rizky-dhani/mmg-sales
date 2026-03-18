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
            if (empty($row['Name'])) {
                continue;
            }

            $initial = $row['Initial'] ?? null;

            Principal::updateOrCreate(
                ['initial' => $initial],
                [
                    'name' => $row['Name'],
                    'principal_acc_code' => $row['Code'] ?? null,
                    'initial' => $initial,
                    'description' => $row['Description'] ?? null,
                    'is_active' => $row['Is_active'] ?? 1,
                ]
            );
        }
    }
}
