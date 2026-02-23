<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Principal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PrincipalProductSeeder extends Seeder
{
    protected array $principalProducts = [];

    protected function loadPrincipalProducts(): void
    {
        $filePath = database_path('../principals_products.txt');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $usedCodes = [];
        $currentPrincipal = null;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (str_starts_with($line, '- ')) {
                if ($currentPrincipal) {
                    $productName = trim(substr($line, 2));
                    $this->principalProducts[$currentPrincipal]['products'][] = $productName;
                }
            } else {
                $currentPrincipal = $line;
                $this->principalProducts[$currentPrincipal] = [
                    'code' => $this->generateCode($line, $usedCodes),
                    'products' => [],
                ];
            }
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

    protected function inferProductType(string $name): string
    {
        $capitalKeywords = [
            'chromatograph', 'hplc', 'spectrofotometer', 'uv vis', 'mass spectrometer',
            'freezer', 'refrigerator', 'centrifuge', 'laminar', 'cabinet', 'safety cabinet',
            'analyzer', 'vortex', 'stirrer', 'infinite', 'spark', 'clia',
        ];

        $nameLower = strtolower($name);

        foreach ($capitalKeywords as $keyword) {
            if (str_contains($nameLower, $keyword)) {
                return 'Capital';
            }
        }

        return 'Consumable';
    }

    protected function generatePrice(string $type, string $name): int
    {
        if ($type === 'Capital') {
            $nameLower = strtolower($name);

            if (str_contains($nameLower, 'chromatograph') || str_contains($nameLower, 'hplc')) {
                return rand(8000000000, 15000000000);
            }
            if (str_contains($nameLower, 'spectrofotometer') || str_contains($nameLower, 'spectrophotometer')) {
                return rand(500000000, 2000000000);
            }
            if (str_contains($nameLower, 'mass spectrometer')) {
                return rand(10000000000, 20000000000);
            }
            if (str_contains($nameLower, 'ultra low') || str_contains($nameLower, 'uluf')) {
                return rand(150000000, 400000000);
            }
            if (str_contains($nameLower, 'refrigerator') || str_contains($nameLower, 'blood bank')) {
                return rand(80000000, 200000000);
            }
            if (str_contains($nameLower, 'centrifuge')) {
                return rand(50000000, 250000000);
            }
            if (str_contains($nameLower, 'laminar') || str_contains($nameLower, 'cabinet') || str_contains($nameLower, 'downflow')) {
                return rand(40000000, 150000000);
            }
            if (str_contains($nameLower, 'analyzer') || str_contains($nameLower, 'infinite') || str_contains($nameLower, 'spark')) {
                return rand(300000000, 800000000);
            }
            if (str_contains($nameLower, 'stirrer') || str_contains($nameLower, 'vortex')) {
                return rand(15000000, 50000000);
            }
            if (str_contains($nameLower, 'clia')) {
                return rand(200000000, 500000000);
            }

            return rand(100000000, 500000000);
        }

        $nameLower = strtolower($name);

        if (str_contains($nameLower, 'kit') || str_contains($nameLower, 'qpcr') || str_contains($nameLower, 'master mix')) {
            return rand(5000000, 25000000);
        }
        if (str_contains($nameLower, 'agar') || str_contains($nameLower, 'broth')) {
            return rand(1000000, 8000000);
        }
        if (str_contains($nameLower, 'swab') || str_contains($nameLower, 'sampling')) {
            return rand(500000, 5000000);
        }
        if (str_contains($nameLower, 'tip') || str_contains($nameLower, 'pipette') || str_contains($nameLower, 'tube')) {
            return rand(500000, 3000000);
        }
        if (str_contains($nameLower, 'cuvette')) {
            return rand(2000000, 10000000);
        }
        if (str_contains($nameLower, 'petridish')) {
            return rand(1000000, 5000000);
        }
        if (str_contains($nameLower, 'igg') || str_contains($nameLower, 'tsh') || str_contains($nameLower, 'd-dimer')) {
            return rand(3000000, 15000000);
        }

        return rand(2000000, 15000000);
    }

    public function run(): void
    {
        $this->loadPrincipalProducts();

        foreach ($this->principalProducts as $principalName => $data) {
            $principal = Principal::where('code', $data['code'])->first();
            if (! $principal) {
                continue;
            }

            foreach ($data['products'] as $productName) {
                $type = $this->inferProductType($productName);
                $price = $this->generatePrice($type, $productName);

                Item::updateOrCreate(
                    ['internal_code' => $data['code'].'-'.preg_replace('/[^A-Za-z0-9]/', '', strtoupper(substr($productName, 0, 8)))],
                    [
                        'name' => $productName,
                        'principal_id' => $principal->id,
                        'internal_code' => $data['code'].'-'.fake()->unique()->numerify('####'),
                        'principle_code' => $data['code'].'-'.strtoupper(Str::random(5)),
                        'unit_price' => $price,
                        'unit' => $type === 'Capital' ? 'Unit' : 'Pack',
                        'description' => $productName.' medical '.strtolower($type),
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
