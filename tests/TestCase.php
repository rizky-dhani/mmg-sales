<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Register Filament service providers for testing
        $app->register(\Filament\FilamentServiceProvider::class);
        $app->register(\Filament\Actions\ActionsServiceProvider::class);
        $app->register(\Filament\Forms\FormsServiceProvider::class);
        $app->register(\Filament\Infolists\InfolistsServiceProvider::class);
        $app->register(\Filament\Notifications\NotificationsServiceProvider::class);
        $app->register(\Filament\Tables\TablesServiceProvider::class);

        return $app;
    }
}
