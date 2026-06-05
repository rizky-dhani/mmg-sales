<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Process;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseBackup extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Database Backup';

    protected static ?string $title = 'Database Backup';

    protected static ?string $slug = 'database-backup';

    protected static string|\UnitEnum|null $navigationGroup = 'System Settings';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('Super Admin') ?? false;
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('runBackup')
                ->label('Run Backup Now')
                ->color('warning')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->requiresConfirmation()
                ->modalHeading('Run Database Backup')
                ->modalDescription('This will create a backup using mysqldump. Continue?')
                ->action(function (): void {
                    $filename = 'mmg_sales-'.now()->format('Y-m-d-H-i-s').'.sql';
                    $dir = storage_path('backups/db');

                    if (! is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }

                    $path = $dir.'/'.$filename;

                    $connection = config('database.default');
                    $user = config("database.connections.{$connection}.username");
                    $password = config("database.connections.{$connection}.password");
                    $host = config("database.connections.{$connection}.host");
                    $port = config("database.connections.{$connection}.port");
                    $database = config("database.connections.{$connection}.database");

                    $result = Process::run("mysqldump --user={$user} --password={$password} --host={$host} --port={$port} {$database} > {$path}");

                    if ($result->successful()) {
                        Notification::make()
                            ->title('Backup completed successfully')
                            ->body("Saved as {$filename}")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Backup failed')
                            ->body($result->errorOutput())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function getBackupFiles(): array
    {
        $dir = storage_path('backups/db');

        if (! is_dir($dir)) {
            return [];
        }

        $files = collect(glob($dir.'/*.sql'))
            ->map(fn (string $path): array => [
                'filename' => basename($path),
                'size' => filesize($path),
                'last_modified' => filemtime($path),
            ])
            ->sortByDesc('last_modified')
            ->values()
            ->toArray();

        return $files;
    }

    public function download(string $filename): BinaryFileResponse
    {
        $path = storage_path('backups/db/'.basename($filename));

        abort_unless(file_exists($path), 404);

        return response()->download($path);
    }

    public function deleteBackup(string $filename): void
    {
        $path = storage_path('backups/db/'.basename($filename));

        if (file_exists($path)) {
            unlink($path);
        }

        Notification::make()
            ->title('Backup deleted successfully')
            ->success()
            ->send();
    }
}
