<?php

namespace App\Services\Reports;

use App\DTOs\CustomerReportData;
use App\DTOs\ReportFilterData;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CustomerReportService
{
    public function generate(ReportFilterData $filters): CustomerReportData
    {
        return Cache::remember(
            "customer_report_{$filters->toCacheKey()}",
            now()->addMinutes(5),
            fn () => $this->calculateReport($filters)
        );
    }

    private function calculateReport(ReportFilterData $filters): CustomerReportData
    {
        $primaryQuery = $this->buildBaseQuery($filters);

        $totalRevenue = (clone $primaryQuery)->sum('total_amount');
        $customerIds = (clone $primaryQuery)->distinct()->pluck('end_customer_id')->filter();
        $totalCustomers = $customerIds->count();

        $newCustomerIds = $this->getNewCustomerIds($filters, $customerIds);
        $newCustomers = $newCustomerIds->count();
        $returningCustomers = $totalCustomers - $newCustomers;

        $averageRevenuePerCustomer = $totalCustomers > 0 ? $totalRevenue / $totalCustomers : 0;

        $comparisonData = $this->getComparisonData($filters);

        return new CustomerReportData(
            topCustomers: $this->getTopCustomers($filters),
            revenueByCustomerGroup: $this->getRevenueByCustomerGroup($filters),
            revenueBySegment: $this->getRevenueBySegment($filters),
            totalCustomers: $totalCustomers,
            newCustomers: $newCustomers,
            returningCustomers: $returningCustomers,
            totalRevenue: $totalRevenue,
            averageRevenuePerCustomer: $averageRevenuePerCustomer,
            monthlyTrend: $this->getMonthlyTrend($filters),
            comparisonTotalRevenue: $comparisonData['totalRevenue'] ?? null,
            comparisonTotalCustomers: $comparisonData['totalCustomers'] ?? null,
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

        if ($filters->customerId) {
            $query->where('end_customer_id', $filters->customerId);
        }

        if ($filters->customerGroupId) {
            $query->where('customer_group_id', $filters->customerGroupId);
        }

        if ($filters->segmentId) {
            $query->where('segment_id', $filters->segmentId);
        }

        if ($filters->cdNcdType) {
            $query->where('cd_ncd_type', $filters->cdNcdType);
        }

        return $query;
    }

    private function getNewCustomerIds(ReportFilterData $filters, Collection $periodCustomerIds): Collection
    {
        $beforePeriodCustomers = Order::query()
            ->where('order_date', '<', $filters->startDate)
            ->distinct()
            ->pluck('end_customer_id');

        return $periodCustomerIds->diff($beforePeriodCustomers);
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

        if ($filters->customerId) {
            $comparisonQuery->where('end_customer_id', $filters->customerId);
        }

        if ($filters->customerGroupId) {
            $comparisonQuery->where('customer_group_id', $filters->customerGroupId);
        }

        $customerIds = (clone $comparisonQuery)->distinct()->pluck('end_customer_id')->filter();

        return [
            'totalRevenue' => (clone $comparisonQuery)->sum('total_amount'),
            'totalCustomers' => $customerIds->count(),
        ];
    }

    private function getTopCustomers(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('end_customer_id, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->with('customer:id,name,cd_ncd_type')
            ->whereNotNull('end_customer_id')
            ->groupBy('end_customer_id')
            ->orderByDesc('revenue')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'customer_id' => $row->end_customer_id,
                'name' => $row->customer?->name ?? 'Unknown',
                'cd_ncd_type' => $row->customer?->cd_ncd_type,
                'revenue' => (float) $row->revenue,
                'orders' => $row->orders,
            ]);
    }

    private function getRevenueByCustomerGroup(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('customer_group_id, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->with('customerGroup:id,name')
            ->whereNotNull('customer_group_id')
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
            ->selectRaw('YEAR(order_date) as year, MONTH(order_date) as month, SUM(total_amount) as revenue, COUNT(DISTINCT end_customer_id) as customers')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'period' => \Carbon\Carbon::create($row->year, $row->month)->format('M Y'),
                'revenue' => (float) $row->revenue,
                'customers' => $row->customers,
            ]);
    }

    public function getExportData(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->with(['customer:id,name,cd_ncd_type', 'customerGroup:id,name', 'segment:id,name'])
            ->orderBy('order_date', 'desc')
            ->get()
            ->map(fn ($order) => [
                'Customer' => $order->customer?->name,
                'Customer Group' => $order->customerGroup?->name,
                'Segment' => $order->segment?->name,
                'CD/NCD Type' => $order->customer?->cd_ncd_type,
                'Order Number' => $order->order_number,
                'Order Date' => $order->order_date?->format('d M Y'),
                'Revenue' => $order->total_amount,
                'Net Sales' => $order->net_sales_total,
                'Payment Status' => ucfirst($order->payment_status),
            ]);
    }
}
