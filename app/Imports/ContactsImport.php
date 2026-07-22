<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ContactsImport implements WithMultipleSheets
{
    public ContactsSheetImport $sheet;

    public function __construct()
    {
        $this->sheet = new ContactsSheetImport;
    }

    public function sheets(): array
    {
        return [
            'Contacts List' => $this->sheet,
        ];
    }
}
