<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Exports\CustomersTemplateExport;
use App\Filament\Actions\ImportCustomersAction;
use App\Filament\Resources\Customers\CustomerResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Facades\Excel;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportCustomersAction::make(),
            Action::make('download_template')
                ->label('Download Template')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->visible(fn () => auth()->user()->hasRole('Super Admin'))
                ->action(fn () => Excel::download(new CustomersTemplateExport, 'customers_template.xlsx')),
            CreateAction::make(),
        ];
    }
}
