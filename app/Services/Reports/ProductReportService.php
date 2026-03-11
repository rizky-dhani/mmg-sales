<?php

namespace App\Services\Reports;

use App\DTOs\ProductReportData;
use App\DTOs\ReportFilterData;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductReportService
{
    public function generate(ReportFilterData $filters): ProductReportData
    {
        return Cache::remember(
            "product_report_{$filters->toCacheKey()}",
            now()->addMinutes(5),
            fn () => $this->calculateReport($filters)
        );
    }

    private function calculateReport(ReportFilterData $filters): ProductReportData
    {
        $primaryQuery = $this->buildBaseQuery($filters);

        $totalQuantity = (clone $primaryQuery)->sum('qty_hna');
        $totalRevenue = (clone $primaryQuery)->sum('total_amount');
        $totalDiscount = (clone $primaryQuery)->sum('discount_amount');

        $comparisonData = $this->getComparisonData($filters);

        return new ProductReportData(
            topItems: $this->getTopItems($filters),
            revenueByPrincipal: $this->getRevenueByPrincipal($filters),
            quantityByItem: $this->getQuantityByItem($filters),
            revenueBySegment: $this->getRevenueBySegment($filters),
            totalQuantity: $totalQuantity,
            totalRevenue: $totalRevenue,
            totalDiscount: $totalDiscount,
            monthlyTrend: $this->getMonthlyTrend($filters),
            comparisonTotalRevenue: $comparisonData['totalRevenue'] ?? null,
            comparisonTotalQuantity: $comparisonData['totalQuantity'] ?? null,
        );
    }

    private function buildBaseQuery(ReportFilterData $filters): \Illuminate\Database\Eloquent\Builder
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

        if ($filters->principalId) {
            $query->where('principal_id', $filters->principalId);
        }

        if ($filters->itemId) {
            $query->where('item_id', $filters->itemId);
        }

        if ($filters->segmentId) {
            $query->where('segment_id', $filters->segmentId);
        }

        if ($filters->salesTypeId) {
            $query->where('sales_type_id', $filters->salesTypeId);
        }

        if ($filters->customerId) {
            $query->where('end_customer_id', $filters->customerId);
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

        if ($filters->principalId) {
            $comparisonQuery->where('principal_id', $filters->principalId);
        }

        if ($filters->itemId) {
            $comparisonQuery->where('item_id', $filters->itemId);
        }

        return [
            'totalRevenue' => (clone $comparisonQuery)->sum('total_amount'),
            'totalQuantity' => (clone $comparisonQuery)->sum('qty_hna'),
        ];
    }

    private function getTopItems(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('item_id, SUM(total_amount) as revenue, SUM(qty_hna) as quantity, COUNT(*) as orders')
            ->with('item:id,name')
            ->whereNotNull('item_id')
            ->groupBy('item_id')
            ->orderByDesc('revenue')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'item_id' => $row->item_id,
                'name' => $row->item?->name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'quantity' => (int) $row->quantity,
                'orders' => $row->orders,
            ]);
    }

    private function getRevenueByPrincipal(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('principal_id, SUM(total_amount) as revenue, SUM(qty_hna) as quantity, COUNT(*) as orders')
            ->with('principal:id,name')
            ->whereNotNull('principal_id')
            ->groupBy('principal_id')
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($row) => [
                'principal_id' => $row->principal_id,
                'name' => $row->principal?->name ?? 'Unknown',
                'revenue' => (float) $row->revenue,
                'quantity' => (int) $row->quantity,
                'orders' => $row->orders,
            ]);
    }

    private function getQuantityByItem(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('item_id, SUM(qty_hna) as quantity')
            ->with('item:id,name')
            ->whereNotNull('item_id')
            ->groupBy('item_id')
            ->orderByDesc('quantity')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'item_id' => $row->item_id,
                'name' => $row->item?->name ?? 'Unknown',
                'quantity' => (int) $row->quantity,
            ]);
    }

    private function getRevenueBySegment(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('segment_id, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->with('segment:id,name')
            ->whereNotNull('segment_id')
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

    private function getMonthlyTrend(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('YEAR(order_date) as year, MONTH(order_date) as month, SUM(total_amount) as revenue, SUM(qty_hna) as quantity')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'period' => \Carbon\Carbon::create($row->year, $row->month)->format('M Y'),
                'revenue' => (float) $row->revenue,
                'quantity' => (int) $row->quantity,
            ]);
    }

    public function getExportData(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->with(['item:id,name', 'principal:id,name', 'segment:id,name'])
            ->orderBy('order_date', 'desc')
            ->get()
            ->map(fn ($order) => [
                'Item' => $order->item?->name,
                'Principal' => $order->principal?->name,
                'Segment' => $order->segment?->name,
                'Order Number' => $order->order_number,
                'Order Date' => $order->order_date?->format('d M Y'),
                'Quantity' => $order->qty_hna,
                'Unit Price' => $order->item?->price ?? 0,
                'Gross Sales' => $order->total_hna_gross_sales,
                'Discount' => $order->discount_amount,
                'Net Sales' => $order->net_sales_total,
                'Total Amount' => $order->total_amount,
            ]);
    }
}
