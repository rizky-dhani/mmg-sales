<?php

namespace App\Filament\Actions;

use App\Imports\OrdersImport;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;

class ImportOrdersAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'importOrders';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Import Orders')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
            ->form([
                FileUpload::make('file')
                    ->label('Excel File')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                    ->disk('public')
                    ->directory('imports')
                    ->required(),
            ])
            ->action(function (array $data): void {
                $file = storage_path('app/public/'.$data['file']);

                Excel::import(new OrdersImport, $file);
            })
            ->successNotificationTitle('Orders imported successfully')
            ->failureNotificationTitle('Import failed');
    }
}
