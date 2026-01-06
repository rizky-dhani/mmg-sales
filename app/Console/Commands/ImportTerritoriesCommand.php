<?php

namespace App\Console\Commands;

use App\Models\Territory;
use App\Services\WilayahApiService;
use Illuminate\Console\Command;

class ImportTerritoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'territories:import {region=java}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import territories from Wilayah.id API';

    /**
     * Execute the console command.
     */
    public function handle(WilayahApiService $api)
    {
        $regionName = ucfirst($this->argument('region'));

        $this->info("Importing territories for region: {$regionName}");

        // 1. Create Region (Level 1)
        $region = Territory::updateOrCreate(
            ['name' => $regionName, 'level' => 1],
            ['type' => 'region']
        );

        // Targeted Java Provinces
        $javaProvinceCodes = ['31', '32', '33', '34', '35', '36'];

        $provinces = $api->getProvinces();

        if (empty($provinces)) {
            $this->error('Failed to fetch provinces from API.');

            return 1;
        }

        $bar = $this->output->createProgressBar(count($javaProvinceCodes));
        $bar->start();

        foreach ($provinces as $provData) {
            if (! in_array($provData['code'], $javaProvinceCodes)) {
                continue;
            }

            // 2. Create Province (Level 2)
            $province = Territory::updateOrCreate(
                ['wilayah_code' => $provData['code'], 'level' => 2],
                [
                    'name' => $provData['name'],
                    'type' => 'province',
                    'parent_id' => $region->id,
                ]
            );

            // 3. Import Cities/Regencies (Level 3)
            $regencies = $api->getRegencies($provData['code']);
            foreach ($regencies as $regData) {
                Territory::updateOrCreate(
                    ['wilayah_code' => $regData['code'], 'level' => 3],
                    [
                        'name' => $regData['name'],
                        'type' => 'city',
                        'parent_id' => $province->id,
                    ]
                );
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Territory import completed.');

        return 0;
    }
}
