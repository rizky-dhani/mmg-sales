<?php

namespace App\Filament\Pages;

use App\Models\Backup;
use App\Services\DatabaseBackupService;
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
                    $backup = app(DatabaseBackupService::class)->run(auth()->id());

                    if ($backup === null) {
                        Notification::make()
                            ->title('Backup failed — dump binary not found or command errored')
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Backup created successfully')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Backup::query()->with('creator')->orderBy('created_at', 'desc'))
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
