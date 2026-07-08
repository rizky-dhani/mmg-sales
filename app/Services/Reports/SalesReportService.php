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

        // Derive gross sales from order_items since total_hna_gross_sales was dropped
        $totalGrossSales = (clone $primaryQuery)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->sum('order_items.subtotal');

        $totalOrders = (clone $primaryQuery)->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $overdueRevenue = 0;

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

        $needsCustomerJoin = $filters->customerGroupId
            || $filters->segmentId
            || $filters->subSegmentId
            || $filters->cdNcdType;

        if ($needsCustomerJoin) {
            $query->leftJoin('customers', 'orders.end_customer_id', '=', 'customers.id');
        }

        if ($filters->territoryId) {
            $query->leftJoin('users', 'orders.created_by', '=', 'users.id')
                ->where('users.territory_id', $filters->territoryId);
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
            $query->where('customers.customer_group_id', $filters->customerGroupId);
        }

        if ($filters->segmentId) {
            $query->where('customers.segment_id', $filters->segmentId);
        }

        if ($filters->subSegmentId) {
            $query->where('customers.sub_segment_id', $filters->subSegmentId);
        }

        if ($filters->itemId) {
            $query->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('order_items.item_id', $filters->itemId);
        }

        if ($filters->leadId) {
            $query->where('lead_id', $filters->leadId);
        }

        if ($filters->cdNcdType) {
            $query->where('customers.cd_ncd_type', $filters->cdNcdType);
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

        $needsCustomerJoin = $filters->customerGroupId
            || $filters->segmentId
            || $filters->subSegmentId
            || $filters->cdNcdType;

        if ($needsCustomerJoin) {
            $comparisonQuery->leftJoin('customers', 'orders.end_customer_id', '=', 'customers.id');
        }

        if ($filters->territoryId) {
            $comparisonQuery->leftJoin('users', 'orders.created_by', '=', 'users.id')
                ->where('users.territory_id', $filters->territoryId);
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
            $comparisonQuery->where('customers.customer_group_id', $filters->customerGroupId);
        }

        if ($filters->segmentId) {
            $comparisonQuery->where('customers.segment_id', $filters->segmentId);
        }

        if ($filters->subSegmentId) {
            $comparisonQuery->where('customers.sub_segment_id', $filters->subSegmentId);
        }

        if ($filters->cdNcdType) {
            $comparisonQuery->where('customers.cd_ncd_type', $filters->cdNcdType);
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
            ->leftJoin('users', 'orders.created_by', '=', 'users.id')
            ->selectRaw('orders.created_by, users.name as user_name, SUM(orders.total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('orders.created_by', 'users.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'user_id' => $row->created_by,
                'name' => $row->user_name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    private function getRevenueByTerritory(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->leftJoin('users', 'orders.created_by', '=', 'users.id')
            ->leftJoin('territories', 'users.territory_id', '=', 'territories.id')
            ->selectRaw('users.territory_id, territories.name as territory_name, SUM(orders.total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('users.territory_id', 'territories.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'territory_id' => $row->territory_id,
                'name' => $row->territory_name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    private function getRevenueByPrincipal(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->leftJoin('principals', 'orders.principal_id', '=', 'principals.id')
            ->selectRaw('orders.principal_id, principals.name as principal_name, SUM(orders.total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('orders.principal_id', 'principals.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'principal_id' => $row->principal_id,
                'name' => $row->principal_name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    private function getRevenueBySegment(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->leftJoin('customers', 'orders.end_customer_id', '=', 'customers.id')
            ->leftJoin('segments', 'customers.segment_id', '=', 'segments.id')
            ->selectRaw('customers.segment_id, segments.name as segment_name, SUM(orders.total_amount) as revenue, COUNT(*) as orders')
            ->whereNotNull('customers.segment_id')
            ->groupBy('customers.segment_id', 'segments.name')
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($row) => [
                'segment_id' => $row->segment_id,
                'name' => $row->segment_name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    private function getRevenueByCustomerGroup(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->leftJoin('customers', 'orders.end_customer_id', '=', 'customers.id')
            ->leftJoin('customer_groups', 'customers.customer_group_id', '=', 'customer_groups.id')
            ->selectRaw('customers.customer_group_id, customer_groups.name as group_name, SUM(orders.total_amount) as revenue, COUNT(*) as orders')
            ->whereNotNull('customers.customer_group_id')
            ->groupBy('customers.customer_group_id', 'customer_groups.name')
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($row) => [
                'customer_group_id' => $row->customer_group_id,
                'name' => $row->group_name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    public function getExportData(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->with(['customer:id,name', 'customer.segment:id,name', 'principal:id,name', 'creator:id,name', 'creator.territory:id,name', 'orderItems.item:id,name'])
            ->orderBy('order_date', 'desc')
            ->get()
            ->map(function ($order) {
                $totalQuantity = $order->orderItems->sum('quantity');
                $totalGrossSales = $order->orderItems->sum('subtotal');
                $itemName = $order->orderItems->first()?->item?->name;

                return [
                    'Order Number' => $order->order_number,
                    'Order Date' => $order->order_date?->format('d M Y'),
                    'Customer' => $order->customer?->name,
                    'Territory' => $order->creator?->territory?->name,
                    'Principal' => $order->principal?->name,
                    'Segment' => $order->customer?->segment?->name,
                    'Item' => $itemName,
                    'Quantity' => $totalQuantity,
                    'Gross Sales' => $totalGrossSales,
                    'Discount' => $order->discount_amount,
                    'Net Sales' => $order->net_sales_total,
                    'Total Amount' => $order->total_amount,
                    'Payment Status' => null,
                    'Sales Rep' => $order->creator?->name,
                ];
            });
    }
}
