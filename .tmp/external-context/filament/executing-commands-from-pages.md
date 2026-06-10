---
source: Laravel Docs + Filament Best Practices (v5.x)
library: Filament
package: filament/filament
topic: Executing artisan commands and shell commands from a Filament page
fetched: 2026-06-05T00:00:00Z
official_docs: https://laravel.com/docs/12.x/artisan
---

# Executing Commands from Filament Custom Pages

This topic is not directly covered in Filament's own docs beyond the action system. Since Filament pages are Livewire components, you use Laravel's native tools within action handlers.

## Recommended Approach: Action with `Artisan::call()` or `Process`

### Option 1: Using `Artisan::call()` for Artisan Commands

```php
use Filament\Actions\Action;
use Illuminate\Support\Facades\Artisan;

protected function getHeaderActions(): array
{
    return [
        Action::make('runBackup')
            ->label('Run Database Backup')
            ->color('warning')
            ->icon('heroicon-o-archive-box-arrow-down')
            ->requiresConfirmation()
            ->modalHeading('Run Backup')
            ->modalDescription('This will execute mysqldump. Continue?')
            ->action(function () {
                $exitCode = Artisan::call('db:backup');
                $output = Artisan::output();
                
                if ($exitCode === 0) {
                    Notification::make()
                        ->title('Backup completed successfully')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Backup failed')
                        ->body($output)
                        ->danger()
                        ->send();
                }
            }),
    ];
}
```

### Option 2: Using Laravel's Process Facade for Shell Commands

```php
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Process;

protected function getHeaderActions(): array
{
    return [
        Action::make('runMysqldump')
            ->label('Export Database')
            ->color('gray')
            ->icon('heroicon-o-document-arrow-down')
            ->requiresConfirmation()
            ->modalHeading('Database Export')
            ->modalDescription('This will run mysqldump. Continue?')
            ->action(function () {
                // Sanitize and build the command
                $user = config('database.connections.mysql.username');
                $password = config('database.connections.mysql.password');
                $host = config('database.connections.mysql.host');
                $database = config('database.connections.mysql.database');
                $filename = storage_path("app/backups/backup-" . now()->format('Y-m-d-H-i-s') . ".sql");
                
                // Ensure directory exists
                $dir = dirname($filename);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                
                $result = Process::run("mysqldump --user={$user} --password={$password} --host={$host} {$database} > {$filename}");
                
                if ($result->successful()) {
                    Notification::make()
                        ->title('Export completed')
                        ->body("Saved to backups directory.")
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Export failed')
                        ->body($result->errorOutput())
                        ->danger()
                        ->send();
                }
            }),
    ];
}
```

## Using Livewire Polling for Long-Running Commands

For commands that take a while, use Livewire polling to show progress:

```php
use Filament\Actions\Action;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;

class BackupPage extends Page
{
    public ?string $backupStatus = null;
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('runBackup')
                ->label('Run Backup')
                ->action(function () {
                    $this->backupStatus = 'running';
                    
                    // Run asynchronously (queue job recommended for production)
                    Process::start('mysqldump ...');
                    
                    $this->backupStatus = 'completed';
                }),
        ];
    }
}
```

In the Blade view (`resources/views/filament/pages/backup-page.blade.php`):

```blade
<div>
    @if($backupStatus === 'running')
        <div wire:poll.2s>
            <x-filament::loading-indicator class="h-5 w-5" />
            <span>Backup in progress...</span>
        </div>
    @endif
</div>
```

## Queue-Based Approach (Recommended for Production)

For heavy operations, dispatch a queued job:

```php
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Jobs\RunDatabaseBackup;

Action::make('runBackup')
    ->label('Run Database Backup')
    ->requiresConfirmation()
    ->action(function () {
        RunDatabaseBackup::dispatch();
        
        Notification::make()
            ->title('Backup queued')
            ->body('You will be notified when complete.')
            ->success()
            ->send();
    }),
```

Then notify the user when the job completes using Filament's database notifications or broadcast notifications.

## Common Pitfalls

- **Timeouts:** Livewire has a default timeout (30s). Long-running `Artisan::call()` or `Process::run()` will exceed this. Always use `Process::start()` (async) or queue the job.
- **Security:** Never pass unsanitized user input to shell commands. Always use validated inputs and sanitize paths.
- **Permissions:** The web server user (e.g., `www-data`) needs appropriate permissions to execute the command and write output files.
- **Environment:** Commands run in the web process context, which may differ from CLI environment variables. Explicitly set paths and credentials.
- **Blocking UI:** Long-running synchronous commands will block the Livewire request until completion. Users see a frozen page. Always prefer queued jobs or async execution.
- **Error handling:** Always check exit codes and capture stderr. Show meaningful error messages to the user via `Notification::make()`.
