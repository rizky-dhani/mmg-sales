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
        public ?float $comparisonTotalRevenue = null,
        public ?float $comparisonTotalQuantity = null,
    ) {}

    public function getRevenueGrowthPercentage(): ?float
    {
        if ($this->comparisonTotalRevenue === null || $this->comparisonTotalRevenue == 0) {
            return null;
        }

        return (($this->totalRevenue - $this->comparisonTotalRevenue) / $this->comparisonTotalRevenue) * 100;
    }

    public function getQuantityGrowthPercentage(): ?float
    {
        if ($this->comparisonTotalQuantity === null || $this->comparisonTotalQuantity == 0) {
            return null;
        }

        return (($this->totalQuantity - $this->comparisonTotalQuantity) / $this->comparisonTotalQuantity) * 100;
    }
}
