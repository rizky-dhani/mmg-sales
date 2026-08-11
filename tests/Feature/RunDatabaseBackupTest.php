<?php

use App\Models\Backup;
use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('backup:run creates a backup row', function (): void
{
    $this->mock(DatabaseBackupService::class, function ($mock): void {
        $mock->shouldReceive('run')->once()->andReturn(Backup::factory()->create());
        $mock->shouldReceive('prune')->once()->with(30)->andReturn(0);
    });

    $this->artisan('backup:run')->assertSuccessful();

    expect(Backup::query()->count())->toBe(1);
});

it('schedules backup:run daily at 02:00', function (): void
{
    $events = collect(app('Illuminate\Console\Scheduling\Schedule')->events())
        ->filter(fn ($event) => str_contains($event->command, 'backup:run'));

    expect($events)->toHaveCount(1)
        ->and((string) $events->first()->expression)->toBe('0 2 * * *');
});
