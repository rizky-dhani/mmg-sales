<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomerListSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithTitle
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

    public function title(): string
    {
        return 'Customer List';
    }
}
