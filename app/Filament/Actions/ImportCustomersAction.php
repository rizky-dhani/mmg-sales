<?php

namespace App\Filament\Actions;

use App\Imports\CustomersImport;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;

class ImportCustomersAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'importCustomers';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Import Customers')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('warning')
            ->can('import_customers')
            ->form([
                FileUpload::make('file')
                    ->label('Excel File')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'application/zip',
                    ])
                    ->disk('public')
                    ->directory('imports')
                    ->required(),
            ])
            ->action(function (array $data): void {
                $file = storage_path('app/public/'.$data['file']);

                Excel::import(new CustomersImport, $file);
            })
            ->successNotificationTitle('Customers imported successfully')
            ->failureNotificationTitle('Import failed');
    }
}
