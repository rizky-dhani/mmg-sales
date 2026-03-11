<?php

namespace Database\Seeders;

use App\Models\Principal;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    protected array $products = [];

    protected function loadProducts(): void
    {
        $filePath = base_path('products.txt');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $this->products[] = $line;
        }
    }

    protected function findPrincipalForProduct(string $productName): ?int
    {
        $principals = Principal::all();

        foreach ($principals as $principal) {
            if (str_contains($productName, $principal->name)) {
                return $principal->id;
            }
        }

        return null;
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
                return rand(800000000, 1500000000);
            }
            if (str_contains($nameLower, 'spectrofotometer') || str_contains($nameLower, 'spectrophotometer')) {
                return rand(500000000, 900000000);
            }
            if (str_contains($nameLower, 'mass spectrometer')) {
                return rand(1000000000, 2000000000);
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
        $this->loadProducts();

        foreach ($this->products as $productName) {
            $type = $this->inferProductType($productName);
            $unitPrice = $this->generatePrice($type, $productName);
            $principalId = $this->findPrincipalForProduct($productName);

            Product::updateOrCreate(
                ['name' => $productName],
                [
                    'category' => $type === 'Capital' ? 'medical_equipment' : 'consumables',
                    'description' => null,
                    'unit_price' => $unitPrice,
                    'unit_of_measure' => $type === 'Capital' ? 'Unit' : 'Pack',
                    'is_active' => true,
                    'principal_id' => $principalId,
                ]
            );
        }
    }
}
