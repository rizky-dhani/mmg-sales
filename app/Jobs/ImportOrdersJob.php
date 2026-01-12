<?php

namespace App\Jobs;

use App\Imports\OrdersImport;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ImportOrdersJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $filePath,
        public int $userId,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            return;
        }

        try {
            Excel::import(new OrdersImport(), $this->filePath);

            Notification::make()
                ->title('Import successful')
                ->body('The orders have been successfully imported from the Excel file.')
                ->success()
                ->sendToDatabase($user);

        } catch (Throwable $e) {
            Notification::make()
                ->title('Import failed')
                ->body('There was an error importing the orders: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->sendToDatabase($user);
        }
    }
}