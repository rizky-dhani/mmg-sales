<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

readonly class SalesReportData
{
    public function __construct(
        public float $totalRevenue,
        public float $totalNetSales,
        public float $totalGrossSales,
        public int $totalOrders,
        public float $averageOrderValue,
        public float $overdueRevenue,
        public Collection $revenueByPeriod,
        public Collection $revenueBySalesRep,
        public Collection $revenueByTerritory,
        public Collection $revenueByPrincipal,
        public Collection $revenueBySegment,
        public Collection $revenueByCustomerGroup,
        public ?float $comparisonTotalRevenue = null,
        public ?float $comparisonTotalNetSales = null,
        public ?int $comparisonTotalOrders = null,
        public ?float $comparisonAverageOrderValue = null,
        public ?float $revenueGrowth = null,
        public ?float $orderGrowth = null,
    ) {}

    public function getRevenueGrowthPercentage(): ?float
    {
        if ($this->comparisonTotalRevenue === null || $this->comparisonTotalRevenue == 0) {
            return null;
        }

        return (($this->totalRevenue - $this->comparisonTotalRevenue) / $this->comparisonTotalRevenue) * 100;
    }

    public function getOrderGrowthPercentage(): ?float
    {
        if ($this->comparisonTotalOrders === null || $this->comparisonTotalOrders == 0) {
            return null;
        }

        return (($this->totalOrders - $this->comparisonTotalOrders) / $this->comparisonTotalOrders) * 100;
    }

    public function getAovGrowthPercentage(): ?float
    {
        if ($this->comparisonAverageOrderValue === null || $this->comparisonAverageOrderValue == 0) {
            return null;
        }

        return (($this->averageOrderValue - $this->comparisonAverageOrderValue) / $this->comparisonAverageOrderValue) * 100;
    }
}
