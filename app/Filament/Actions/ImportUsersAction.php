<?php

namespace App\Filament\Actions;

use App\Imports\UsersImport;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;

class ImportUsersAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'importUsers';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Import Users')
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

                Excel::import(new UsersImport, $file);
            })
            ->successNotificationTitle('Users imported successfully')
            ->failureNotificationTitle('Import failed');
    }
}
