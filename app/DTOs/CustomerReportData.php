<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

readonly class CustomerReportData
{
    public function __construct(
        public Collection $topCustomers,
        public Collection $revenueByCustomerGroup,
        public Collection $revenueBySegment,
        public int $totalCustomers,
        public int $newCustomers,
        public int $returningCustomers,
        public float $totalRevenue,
        public float $averageRevenuePerCustomer,
        public Collection $monthlyTrend,
        public ?float $comparisonTotalRevenue = null,
        public ?int $comparisonTotalCustomers = null,
    ) {}

    public function getRevenueGrowthPercentage(): ?float
    {
        if ($this->comparisonTotalRevenue === null || $this->comparisonTotalRevenue == 0) {
            return null;
        }

        return (($this->totalRevenue - $this->comparisonTotalRevenue) / $this->comparisonTotalRevenue) * 100;
    }

    public function getCustomerGrowthPercentage(): ?float
    {
        if ($this->comparisonTotalCustomers === null || $this->comparisonTotalCustomers == 0) {
            return null;
        }

        return (($this->totalCustomers - $this->comparisonTotalCustomers) / $this->comparisonTotalCustomers) * 100;
    }
}
