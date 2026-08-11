<?php

use App\Models\Backup;
use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('prunes backups older than keep days', function (): void
{
    $dir = storage_path('backups/db');
    $old = Backup::factory()->create(['created_at' => now()->subDays(31)]);
    $new = Backup::factory()->create(['created_at' => now()]);

    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($dir.'/'.$old->filename, 'old');
    file_put_contents($dir.'/'.$new->filename, 'new');

    $deleted = app(DatabaseBackupService::class)->prune(30);

    expect($deleted)->toBe(1)
        ->and(Backup::find($old->id))->toBeNull()
        ->and(Backup::find($new->id))->not->toBeNull()
        ->and(file_exists($dir.'/'.$old->filename))->toBeFalse()
        ->and(file_exists($dir.'/'.$new->filename))->toBeTrue();
});