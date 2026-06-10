---
source: Filament Official Docs (v5.x) + Laravel Docs
library: Filament
package: filament/filament
topic: File downloads from custom pages
fetched: 2026-06-05T00:00:00Z
official_docs: https://filamentphp.com/docs/5.x/actions/overview
---

# File Downloads from Custom Filament Pages

## Option 1: Action with `->url()` pointing to a download route

Create a Laravel route that returns a file download, then link an action to it:

```php
use Filament\Actions\Action;

protected function getHeaderActions(): array
{
    return [
        Action::make('downloadBackup')
            ->label('Download Latest Backup')
            ->icon('heroicon-o-arrow-down-tray')
            ->url(route('filament.backups.download', ['filename' => 'latest.sql']))
            ->openUrlInNewTab(),
    ];
}
```

In your routes file (`routes/web.php`):

```php
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

Route::get('/admin/downloads/backup/{filename}', function (string $filename) {
    $path = storage_path("app/backups/{$filename}");
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->download($path);
})->name('filament.backups.download')->middleware(['auth', 'can:manage-backups']);
```

## Option 2: Action with `->action()` returning a StreamedResponse

Use an action handler that calls `$this->download()` (available on Livewire components):

**Note:** In Filament v5, pages are Livewire components. You can use Livewire's file download capabilities:

```php
use Filament\Actions\Action;

protected function getHeaderActions(): array
{
    return [
        Action::make('exportCsv')
            ->label('Export CSV')
            ->icon('heroicon-o-document-text')
            ->action(function () {
                $csvData = "Name,Email,Created At\n";
                // ... build CSV content ...
                
                return response()->streamDownload(
                    function () use ($csvData) {
                        echo $csvData;
                    },
                    'users-export.csv',
                    ['Content-Type' => 'text/csv']
                );
            }),
    ];
}
```

**Important:** Calling `return response()->download(...)` or `return response()->streamDownload(...)` inside a Filament action callback will work because Filament actions can return `Symfony\Component\HttpFoundation\Response` objects, which Livewire forwards as a file download.

## Option 3: Using Laravel's Storage Downloads

```php
Action::make('downloadReport')
    ->label('Download Report')
    ->action(function () {
        $path = 'reports/monthly-report.pdf';
        
        if (!Storage::disk('local')->exists($path)) {
            Notification::make()
                ->title('File not found')
                ->danger()
                ->send();
            return;
        }
        
        return Storage::disk('local')->download($path);
    }),
```

## Option 4: Form File Upload `->downloadable()`

For file upload fields within forms on pages:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('attachments')
    ->multiple()
    ->downloadable()
```

Custom download URL for form file uploads:

```php
FileUpload::make('attachments')
    ->multiple()
    ->downloadable()
    ->getDownloadableFileUrlUsing(fn (string $file): string => route('attachments.download', ['path' => $file]))
```

Note: this is for form file upload fields within a page schema, not for general-purpose downloads.

## Option 5: Export Action (Table Data)

If you need to export table data (e.g., from a table on a custom page), use Filament's dedicated Export action:

```php
use Filament\Actions\ExportAction;
use App\Filament\Exports\UserExporter;

ExportAction::make()
    ->exporter(UserExporter::class)
```

Export-generated files are authorized by default to only the user who started the export (customizable via `ExportPolicy`).

## Common Pitfalls

- When returning a `StreamedResponse` or `BinaryFileResponse` from an action callback, ensure the action does NOT also call other response-modifying methods (like `->successNotificationTitle()` on success) — the file download IS the response.
- Large files (>100MB) should be streamed with `response()->streamDownload()` rather than `response()->download()` to avoid memory exhaustion.
- Always validate that the file exists and the user is authorized BEFORE returning the download.
- For export-generated files, authorization is per-user by default. If you need shared downloads, register an `ExportPolicy`.
- Security: Never accept raw user input as file paths. Always validate/sanitize filenames and resolve paths against a known base directory.
- File downloads from Livewire components may fail silently if the response builder is misconfigured. Use `response()->download()` or `Storage::download()` which return proper `BinaryFileResponse` objects.
