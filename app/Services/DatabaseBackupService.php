<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\Process;

class DatabaseBackupService
{
    protected ?string $binary = null;

    protected ?bool $isMaria = null;

    public function run(?int $userId = null): ?Backup
    {
        $binary = $this->findDumpBinary();

        if ($binary === null) {
            return null;
        }

        $filename = 'mmg_sales-'.now()->format('Y-m-d-H-i-s').'.sql';
        $path = storage_path('backups/db/'.$filename);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $command = $this->dumpCommand($binary, $path);

        $result = Process::run($command);

        if ($result->failed() || ! file_exists($path)) {
            @unlink($path);

            return null;
        }

        return Backup::create([
            'filename' => $filename,
            'size' => filesize($path),
            'created_by' => $userId,
        ]);
    }

    public function prune(int $keepDays = 30): int
    {
        $cutoff = now()->subDays($keepDays);
        $dir = storage_path('backups/db');
        $deleted = 0;

        Backup::query()
            ->where('created_at', '<', $cutoff)
            ->get()
            ->each(function (Backup $backup) use ($dir, &$deleted): void {
                $path = $dir.'/'.$backup->filename;

                if (file_exists($path)) {
                    unlink($path);
                }

                $backup->delete();
                $deleted++;
            });

        return $deleted;
    }

    protected function findDumpBinary(): ?string
    {
        if ($this->binary) {
            return $this->binary;
        }

        $candidates = ['mariadb-dump', 'mysqldump'];

        foreach ($candidates as $binary) {
            $result = Process::run('which '.$binary.' 2>/dev/null');

            if ($result->successful() && ($path = trim($result->output())) !== '') {
                $this->binary = $path;

                return $path;
            }
        }

        return null;
    }

    protected function dumpCommand(string $binary, string $path): string
    {
        $host = config('database.connections.'.config('database.default').'.host');
        $port = config('database.connections.'.config('database.default').'.port');
        $database = config('database.connections.'.config('database.default').'.database');
        $username = config('database.connections.'.config('database.default').'.username');
        $password = config('database.connections.'.config('database.default').'.password');

        $cmd = sprintf(
            '"%s" --host=%s --port=%s --user=%s --password=%s %s > "%s"',
            $binary,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            $path
        );

        if ($this->isMaria()) {
            $cmd .= ' --skip-column-statistics';
        }

        return $cmd;
    }

    protected function isMaria(): bool
    {
        if ($this->isMaria !== null) {
            return $this->isMaria;
        }

        $binary = $this->findDumpBinary();

        if ($binary === null) {
            return $this->isMaria = false;
        }

        $result = Process::run('"'.$binary.'" --version 2>/dev/null');

        return $this->isMaria = str_contains($result->output(), 'MariaDB');
    }
}
