<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    protected array $products = [
        'Infitek Liquid Chromatograph (HPLC-3500)',
        'Infitek Laminar Flow Cabinet (LCB-V6F)',
        'Infitek UV VIS Spectrofotometer (SP-LUV1910)',
        'ARCTIKO ULUF 450-2M - Ultra Low Temp Freezer',
        'ARCTIKO BBR 500-D Blood Bank Refrigerator',
        'ARCTIKO LRE 490 - Refrigerator',
        'AFI ISA Centrifuge',
        'AFI SIRENA Centrifuge Refrigerated',
        'AFI LOREENA Centrifuge Refrigerated',
        'ACE 3000',
        'Ion Molecule Reaction Mass Spectrometer',
        'Biochem Plate count agar PCA',
        'Biochem Violet Red Bile Agar',
        'Biocomma Mannitol Salt Agar',
        'Biocomma Trypticase Soy Broth',
        'Biocomma Macconkey Agar',
        'Coolfinity IceVolt 300P',
        'Coolfinity Icevolt 300',
        'FL Medical Promed Petridish',
        'Promed Test Tube',
        'FL Medical Promed Tips and Pipette',
        'Oxford Benchmate Vortex Mixer',
        'Oxford Benchmate Magnetic Stirrers',
        'Oxford Slimline Motorized Stirrer',
        'Monmouth Circulaire Downflow & Formalin Containment',
        'Monmouth Laminar Air Flow Cabinets',
        'Monmouth Class II Biological Safety Cabinets',
        'NX-48S Plant DNA Kit',
        'NX-48S Tissue DNA Kit',
        'GENOLUTION NX-48S Viral NA Kit',
        'SMARTCHEK Foodborn Pathogen Detection Kits',
        'SMARTCHEK Halal Food Testing Kit',
        'SMARTCHEK FMDV (Foot and Mouth Disease Virus) Detection Kit',
        'Peky Bio Tube',
        'Peky Bio Serological Pipette',
        'Peky Bio Pipette Tips',
        'HOT FIREPol Evagreen qPCR Mix Plus',
        'FIREPol Master Mix Ready to Load 12,5 mM MgCl2',
        'HOT FIREPol EvaGreen qPCR Supermix',
        'Infinite 200Pro',
        'SPARK',
        'Infinite F50',
        'DIAPRO RUB IgG',
        'DIAPRO TOXO IgG',
        'DIAPRO Chlamydia trachomatis IgG',
        'DiaSpect Hemoglobin Cuvette',
        'Diaspect TM Analyzer and Accessories',
        'DiaSpect Hemoglobin Cuvette-EKF',
        'NODFORD CLIA STA-60',
        'NODFORD D-Dimer (CLIA Kit)',
        'Nodford Thyroid-Stimulating Hormone/ TSH (CLIA) Kit',
        'MANDELAB Disposable HPV Test Sampling Swabs',
        'MANDELAB Disposable Sampling Kit for HPV Collection & Preservation Kit',
        'MANDELAB Disposable Sampling Cervical Brush (Escopa)',
    ];

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
        foreach ($this->products as $productName) {
            $type = $this->inferProductType($productName);
            $unitPrice = $this->generatePrice($type, $productName);

            Product::create([
                'name' => $productName,
                'sku' => str()->slug($productName).'-'.str()->random(4),
                'category' => $type === 'Capital' ? 'medical_equipment' : 'consumables',
                'description' => null,
                'unit_price' => $unitPrice,
                'currency' => 'PHP',
                'unit_of_measure' => $type === 'Capital' ? 'Unit' : 'Pack',
                'stock_quantity' => 0,
                'minimum_stock' => 0,
                'reorder_quantity' => 0,
                'is_active' => true,
                'requires_prescription' => false,
                'manufacturer' => null,
                'expiry_date' => null,
                'storage_requirements' => null,
                'principal_id' => null,
            ]);
        }
    }
}
