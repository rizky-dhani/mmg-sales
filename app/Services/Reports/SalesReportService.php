<?php

namespace App\Services\Reports;

use App\DTOs\ReportFilterData;
use App\DTOs\SalesReportData;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SalesReportService
{
    public function generate(ReportFilterData $filters): SalesReportData
    {
        return Cache::remember(
            "sales_report_{$filters->toCacheKey()}",
            now()->addMinutes(5),
            fn () => $this->calculateReport($filters)
        );
    }

    private function calculateReport(ReportFilterData $filters): SalesReportData
    {
        $primaryQuery = $this->buildBaseQuery($filters);

        $totalRevenue = (clone $primaryQuery)->sum('total_amount');
        $totalNetSales = (clone $primaryQuery)->sum('net_sales_total');
        $totalGrossSales = (clone $primaryQuery)->sum('total_hna_gross_sales');
        $totalOrders = (clone $primaryQuery)->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $overdueRevenue = (clone $primaryQuery)->where('payment_status', 'overdue')->sum('net_sales_total');

        $comparisonData = $this->getComparisonData($filters);

        return new SalesReportData(
            totalRevenue: $totalRevenue,
            totalNetSales: $totalNetSales,
            totalGrossSales: $totalGrossSales,
            totalOrders: $totalOrders,
            averageOrderValue: $averageOrderValue,
            overdueRevenue: $overdueRevenue,
            revenueByPeriod: $this->getRevenueByPeriod($filters),
            revenueBySalesRep: $this->getRevenueBySalesRep($filters),
            revenueByTerritory: $this->getRevenueByTerritory($filters),
            revenueByPrincipal: $this->getRevenueByPrincipal($filters),
            revenueBySegment: $this->getRevenueBySegment($filters),
            revenueByCustomerGroup: $this->getRevenueByCustomerGroup($filters),
            comparisonTotalRevenue: $comparisonData['totalRevenue'] ?? null,
            comparisonTotalNetSales: $comparisonData['totalNetSales'] ?? null,
            comparisonTotalOrders: $comparisonData['totalOrders'] ?? null,
            comparisonAverageOrderValue: $comparisonData['averageOrderValue'] ?? null,
        );
    }

    private function buildBaseQuery(ReportFilterData $filters): Builder
    {
        $query = Order::query()
            ->whereBetween('order_date', [$filters->startDate, $filters->endDate]);

        if ($filters->userId) {
            $query->where('created_by', $filters->userId);
        }

        if (! empty($filters->userIds)) {
            $query->whereIn('created_by', $filters->userIds);
        }

        if ($filters->territoryId) {
            $query->where('area_city_id', $filters->territoryId);
        }

        if ($filters->departmentId) {
            $query->where('department_id', $filters->departmentId);
        }

        if ($filters->principalId) {
            $query->where('principal_id', $filters->principalId);
        }

        if ($filters->distributorId) {
            $query->where('distributor_id', $filters->distributorId);
        }

        if ($filters->customerId) {
            $query->where('end_customer_id', $filters->customerId);
        }

        if ($filters->customerGroupId) {
            $query->where('customer_group_id', $filters->customerGroupId);
        }

        if ($filters->segmentId) {
            $query->where('segment_id', $filters->segmentId);
        }

        if ($filters->subSegmentId) {
            $query->where('sub_segment_id', $filters->subSegmentId);
        }

        if ($filters->itemId) {
            $query->where('item_id', $filters->itemId);
        }

        if ($filters->leadId) {
            $query->where('lead_id', $filters->leadId);
        }

        if ($filters->orderStatus) {
            $query->where('status', $filters->orderStatus);
        }

        if ($filters->paymentStatus) {
            $query->where('payment_status', $filters->paymentStatus);
        }

        if ($filters->cdNcdType) {
            $query->where('cd_ncd_type', $filters->cdNcdType);
        }

        return $query;
    }

    private function getComparisonData(ReportFilterData $filters): array
    {
        if (! $filters->hasComparison()) {
            return [];
        }

        $comparisonQuery = Order::query()
            ->whereBetween('order_date', [$filters->comparisonStartDate, $filters->comparisonEndDate]);

        if ($filters->userId) {
            $comparisonQuery->where('created_by', $filters->userId);
        }

        if (! empty($filters->userIds)) {
            $comparisonQuery->whereIn('created_by', $filters->userIds);
        }

        if ($filters->territoryId) {
            $comparisonQuery->where('area_city_id', $filters->territoryId);
        }

        if ($filters->departmentId) {
            $comparisonQuery->where('department_id', $filters->departmentId);
        }

        if ($filters->principalId) {
            $comparisonQuery->where('principal_id', $filters->principalId);
        }

        if ($filters->customerId) {
            $comparisonQuery->where('end_customer_id', $filters->customerId);
        }

        if ($filters->customerGroupId) {
            $comparisonQuery->where('customer_group_id', $filters->customerGroupId);
        }

        if ($filters->segmentId) {
            $comparisonQuery->where('segment_id', $filters->segmentId);
        }

        $totalRevenue = (clone $comparisonQuery)->sum('total_amount');
        $totalOrders = (clone $comparisonQuery)->count();

        return [
            'totalRevenue' => $totalRevenue,
            'totalNetSales' => (clone $comparisonQuery)->sum('net_sales_total'),
            'totalOrders' => $totalOrders,
            'averageOrderValue' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0,
        ];
    }

    private function getRevenueByPeriod(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('YEAR(order_date) as year, MONTH(order_date) as month, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'period' => Carbon::create($row->year, $row->month)->format('M Y'),
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    private function getRevenueBySalesRep(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('created_by, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->with('creator:id,name')
            ->groupBy('created_by')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'user_id' => $row->created_by,
                'name' => $row->creator?->name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    private function getRevenueByTerritory(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('area_city_id, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->with('territory:id,name')
            ->groupBy('area_city_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'territory_id' => $row->area_city_id,
                'name' => $row->territory?->name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    private function getRevenueByPrincipal(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('principal_id, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->with('principal:id,name')
            ->groupBy('principal_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'principal_id' => $row->principal_id,
                'name' => $row->principal?->name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    private function getRevenueBySegment(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('segment_id, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->with('segment:id,name')
            ->groupBy('segment_id')
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($row) => [
                'segment_id' => $row->segment_id,
                'name' => $row->segment?->name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    private function getRevenueByCustomerGroup(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('customer_group_id, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->with('customerGroup:id,name')
            ->groupBy('customer_group_id')
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($row) => [
                'customer_group_id' => $row->customer_group_id,
                'name' => $row->customerGroup?->name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    public function getExportData(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->with(['customer:id,name', 'territory:id,name', 'principal:id,name', 'segment:id,name', 'creator:id,name'])
            ->orderBy('order_date', 'desc')
            ->get()
            ->map(fn ($order) => [
                'Order Number' => $order->order_number,
                'Order Date' => $order->order_date?->format('d M Y'),
                'Customer' => $order->customer?->name,
                'Territory' => $order->territory?->name,
                'Principal' => $order->principal?->name,
                'Segment' => $order->segment?->name,
                'Item' => $order->item?->name,
                'Quantity' => $order->qty_hna,
                'Gross Sales' => $order->total_hna_gross_sales,
                'Discount' => $order->discount_amount,
                'Net Sales' => $order->net_sales_total,
                'Total Amount' => $order->total_amount,
                'Status' => ucfirst($order->status),
                'Payment Status' => ucfirst($order->payment_status),
                'Sales Rep' => $order->creator?->name,
            ]);
    }
}
