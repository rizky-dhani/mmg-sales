<?php

namespace Database\Seeders;

use App\Models\Principal;
use Illuminate\Database\Seeder;

class PrincipalSeeder extends Seeder
{
    protected array $principals = [];

    protected function loadPrincipals(): void
    {
        $filePath = base_path('principals.txt');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $usedCodes = [];
        $currentPrincipal = null;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (str_starts_with($line, '- ')) {
                continue;
            }

            $currentPrincipal = $line;
            $this->principals[$currentPrincipal] = [
                'code' => $this->generateCode($line, $usedCodes),
            ];
        }
    }

    protected function generateCode(string $name, array &$usedCodes): string
    {
        $words = preg_split('/\s+/', $name);
        if (count($words) >= 2) {
            $code = strtoupper(substr($words[0], 0, 2).substr($words[1], 0, 1));
        } else {
            $code = strtoupper(substr($name, 0, 3));
        }

        if (isset($usedCodes[$code])) {
            $counter = 1;
            while (isset($usedCodes[$code.$counter])) {
                $counter++;
            }
            $code = $code.$counter;
        }

        $usedCodes[$code] = true;

        return $code;
    }

    public function run(): void
    {
        $this->loadPrincipals();

        foreach ($this->principals as $name => $data) {
            Principal::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $name,
                    'code' => $data['code'],
                    'description' => $name.' Medical Equipment and Supplies',
                    'contact_person' => fake('id_ID')->name(),
                    'phone' => fake('id_ID')->phoneNumber(),
                    'email' => strtolower($data['code']).'@'.strtolower(str_replace(' ', '', $name)).'.com',
                    'address' => fake('id_ID')->address(),
                    'is_active' => true,
                ]
            );
        }
    }
}
