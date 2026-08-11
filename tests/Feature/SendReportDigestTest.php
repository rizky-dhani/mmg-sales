<?php

use App\Mail\ReportDigestMail;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('sends digest to users of given role', function (): void
{
    $this->seed(RolesAndPermissionsSeeder::class);

    Mail::fake();

    $director = User::factory()->create();
    $director->assignRole('Management Director');
    User::factory()->create()->assignRole('Sales Staff');

    $this->artisan('reports:send-digest', ['--role' => 'Management Director', '--period' => 'weekly'])
        ->assertSuccessful();

    Mail::assertSent(ReportDigestMail::class, 1);
    Mail::assertSent(ReportDigestMail::class, fn (ReportDigestMail $m) => $m->hasTo($director->email) && count($m->attachments()) === 2);
});

it('schedules reports:send-digest weekly on monday 07:00', function (): void
{
    $events = collect(app('Illuminate\Console\Scheduling\Schedule')->events())
        ->filter(fn ($event) => str_contains($event->command, 'reports:send-digest'));

    expect($events)->toHaveCount(1)
        ->and((string) $events->first()->expression)->toBe('0 7 * * 1');
});
