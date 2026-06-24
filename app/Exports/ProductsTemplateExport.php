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
                'internal_code' => '',
                'unit_price' => 100000,
                'ecatalog_price' => 95000,
                'principal_id' => 1,
                'principal_name' => 'Example Principal',
                'unit_of_measure' => 'pcs',
                'is_active' => 1,
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'category',
            'description',
            'internal_code',
            'unit_price',
            'ecatalog_price',
            'principal_id',
            'principal_name',
            'unit_of_measure',
            'is_active',
        ];
    }
}
