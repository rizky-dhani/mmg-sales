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
    ) {}
}
