<?php

namespace Database\Seeders;

use App\Models\Segment;
use App\Models\SubSegment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'PRIVATE LAB - CD' => [
                'PRIVATE LAB-CD',
            ],
            'INDUSTRY' => [
                'PHARMA & VACCINES',
                'CHEMICAL',
                'LIVESTOCK & FEED',
                'Agriculture Industry',
            ],
            'UNIVERSITY' => [
                'UNIV-Negeri',
                'UNIV-Swasta',
            ],
            'HOSPITAL' => [
                'HOSPITAL',
                'UNIV-Negeri',
            ],
            'SUPPLIER' => [
                'SUPPLIER',
            ],
            'GOVERNMENT LAB - NCD' => [
                'GOVERNMENT LAB - NCD',
            ],
            'GOVERNMENT LAB - CD' => [
                'GOVERNMENT LAB - CD',
            ],
            'PRIVATE LAB - NCD' => [
                'PRIVATE LAB-NCD',
                'BLOOD BANK',
            ],
            'DINKES/PKM' => [
                'DINKES/MOH',
                'PUSKESMAS',
            ],
            'KLINIK' => [
                'KLINIK',
            ],
        ];

        foreach ($data as $segmentName => $subSegments) {
            $segment = Segment::updateOrCreate(
                ['name' => $segmentName],
                ['code' => Str::slug($segmentName)]
            );

            foreach ($subSegments as $subSegmentName) {
                SubSegment::updateOrCreate(
                    [
                        'name' => $subSegmentName,
                        'segment_id' => $segment->id,
                    ],
                    ['code' => Str::slug($segmentName.'-'.$subSegmentName)]
                );
            }
        }
    }
}
