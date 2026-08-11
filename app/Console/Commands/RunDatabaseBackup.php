<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class RunDatabaseBackup extends Command
{
    protected $signature = 'backup:run {--keep=30 : Days of backups to retain}';

    protected $description = 'Create a database backup and prune old ones';

    public function handle(DatabaseBackupService $service): int
    {
        $backup = $service->run();

        if ($backup === null) {
            $this->error('Backup failed');

            return self::FAILURE;
        }

        $pruned = $service->prune((int) $this->option('keep'));

        $this->info("Backup created: {$backup->filename}");
        $this->info("Pruned {$pruned} old backup(s)");

        return self::SUCCESS;
    }
}
