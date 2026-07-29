<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CustomersImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Customers' => new CustomersSheetImport,
        ];
    }
}
