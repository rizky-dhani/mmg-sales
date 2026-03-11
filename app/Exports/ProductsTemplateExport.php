<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsTemplateExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return collect([
            [
                'name' => 'Example Product',
                'category' => 'medical_equipment',
                'description' => 'Product description',
                'unit_price' => 100000,
                'unit_of_measure' => 'pcs',
                'is_active' => 1,
                'principal_id' => 1,
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'category',
            'description',
            'unit_price',
            'unit_of_measure',
            'is_active',
            'principal_id',
        ];
    }
}
