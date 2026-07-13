<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerListSheet implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Customer::select('id', 'name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return ['id', 'name'];
    }
}
