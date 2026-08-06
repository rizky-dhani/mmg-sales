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
    ) {}
}
