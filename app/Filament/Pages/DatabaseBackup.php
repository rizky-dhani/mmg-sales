<?php

namespace App\Filament\Pages;

use App\Models\Backup;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Process;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseBackup extends Page implements HasTable
{
    use InteractsWithTable;

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
                ->modalDescription('This will dump the database and save a .sql file to storage. Continue?')
                ->action(function (): void {
                    $dumpBinary = $this->findDumpBinary();

                    if ($dumpBinary === null) {
                        Notification::make()
                            ->title('Database dump tool not found')
                            ->body('Install mysql-client or mariadb-client (e.g., apt install mariadb-client).')
                            ->danger()
                            ->send();

                        return;
                    }

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

                    $result = Process::run("{$dumpBinary} --user={$user} --password=".escapeshellarg($password)." --host={$host} --port={$port} {$database} > {$path}");

                    if ($result->successful()) {
                        Backup::create([
                            'filename' => $filename,
                            'size' => file_exists($path) ? filesize($path) : 0,
                            'created_by' => auth()->id(),
                        ]);

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

    private function findDumpBinary(): ?string
    {
        $candidates = ['mariadb-dump', 'mysqldump'];

        // Check using `which` for binaries on PATH
        foreach ($candidates as $binary) {
            $result = Process::run("which {$binary} 2>/dev/null");

            if ($result->successful() && ($path = trim($result->output())) !== '') {
                return $path;
            }
        }

        // Fallback: common installation paths
        $commonPaths = [
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/usr/bin/mariadb-dump',
            '/usr/local/bin/mariadb-dump',
        ];

        foreach ($commonPaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }

        return null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Backup::query()->with('creator'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('filename')
                    ->label('Filename')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn (int $state): string => $state > 1024 * 1024
                        ? number_format($state / 1024 / 1024, 2).' MB'
                        : number_format($state / 1024, 1).' KB')
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Created by')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('download')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->action(function (Backup $record): BinaryFileResponse {
                            $path = storage_path('backups/db/'.basename($record->filename));

                            abort_unless(file_exists($path), 404);

                            return response()->download($path);
                        }),
                    Action::make('delete')
                        ->label('Delete')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Backup $record): void {
                            $path = storage_path('backups/db/'.basename($record->filename));

                            if (file_exists($path)) {
                                unlink($path);
                            }

                            $record->delete();

                            Notification::make()
                                ->title('Backup deleted successfully')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }
}
