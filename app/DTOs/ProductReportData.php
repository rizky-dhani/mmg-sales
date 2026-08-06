<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

readonly class ProductReportData
{
    public function __construct(
        public Collection $topItems,
        public Collection $revenueByPrincipal,
        public Collection $quantityByItem,
        public Collection $revenueBySegment,
        public float $totalQuantity,
        public float $totalRevenue,
        public float $totalDiscount,
        public Collection $monthlyTrend,
    ) {}
}
