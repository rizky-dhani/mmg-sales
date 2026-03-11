<?php

namespace App\Filament\Actions;

use App\Imports\ProductsImport;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;

class ImportProductsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'importProducts';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Import Products')
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

                Excel::import(new ProductsImport, $file);
            })
            ->successNotificationTitle('Products imported successfully')
            ->failureNotificationTitle('Import failed');
    }
}
