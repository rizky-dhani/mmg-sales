<?php

namespace App\Services\Reports;

use App\DTOs\PipelineReportData;
use App\DTOs\ReportFilterData;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PipelineReportService
{
    public function generate(ReportFilterData $filters): PipelineReportData
    {
        return Cache::remember(
            "pipeline_report_{$filters->toCacheKey()}",
            now()->addMinutes(5),
            fn () => $this->calculateReport($filters)
        );
    }

    private function calculateReport(ReportFilterData $filters): PipelineReportData
    {
        $primaryQuery = $this->buildBaseQuery($filters);

        $totalProjects = (clone $primaryQuery)->count();
        $wonProjects = (clone $primaryQuery)->where('status', 'won')->count();
        $lostProjects = (clone $primaryQuery)->where('status', 'lost')->count();

        $totalPipelineValue = (clone $primaryQuery)->sum('estimated_revenue');
        $wonValue = (clone $primaryQuery)->where('status', 'won')->sum('estimated_revenue');
        $lostValue = (clone $primaryQuery)->where('status', 'lost')->sum('estimated_revenue');

        $winRate = ($wonProjects + $lostProjects) > 0
            ? ($wonProjects / ($wonProjects + $lostProjects)) * 100
            : 0;

        $averageDealSize = $wonProjects > 0 ? $wonValue / $wonProjects : 0;
        $averageSalesCycle = $this->calculateAverageSalesCycle($filters);

        $comparisonData = $this->getComparisonData($filters);

        return new PipelineReportData(
            totalPipelineValue: $totalPipelineValue,
            wonValue: $wonValue,
            lostValue: $lostValue,
            totalProjects: $totalProjects,
            wonProjects: $wonProjects,
            lostProjects: $lostProjects,
            winRate: $winRate,
            averageDealSize: $averageDealSize,
            averageSalesCycle: $averageSalesCycle,
            pipelineByStatus: $this->getPipelineByStatus($filters),
            pipelineBySalesRep: $this->getPipelineBySalesRep($filters),
            monthlyTrend: $this->getMonthlyTrend($filters),
            recentWins: $this->getRecentWins($filters),
            recentLosses: $this->getRecentLosses($filters),
            comparisonTotalPipelineValue: $comparisonData['totalPipelineValue'] ?? null,
            comparisonWinRate: $comparisonData['winRate'] ?? null,
        );
    }

    private function buildBaseQuery(ReportFilterData $filters): Builder
    {
        $query = Lead::query()
            ->whereBetween('leads.created_at', [$filters->startDate, $filters->endDate]);

        if ($filters->userId) {
            $query->whereHas('collaborators', fn ($q) => $q->where('user_id', $filters->userId));
        }

        if (! empty($filters->userIds)) {
            $query->whereHas('collaborators', fn ($q) => $q->whereIn('user_id', $filters->userIds));
        }

        if ($filters->customerId) {
            $query->where('customer_id', $filters->customerId);
        }

        if ($filters->leadStatus) {
            $query->where('status', $filters->leadStatus);
        }

        if ($filters->leadSource) {
            $query->where('source', $filters->leadSource);
        }

        if ($filters->leadPriority) {
            $query->where('priority', $filters->leadPriority);
        }

        return $query;
    }

    private function getComparisonData(ReportFilterData $filters): array
    {
        if (! $filters->hasComparison()) {
            return [];
        }

        $comparisonQuery = Lead::query()
            ->whereBetween('created_at', [$filters->comparisonStartDate, $filters->comparisonEndDate]);

        if ($filters->userId) {
            $comparisonQuery->where('assigned_to', $filters->userId);
        }

        if (! empty($filters->userIds)) {
            $comparisonQuery->whereIn('assigned_to', $filters->userIds);
        }

        if ($filters->leadSource) {
            $comparisonQuery->where('source', $filters->leadSource);
        }

        if ($filters->leadPriority) {
            $comparisonQuery->where('priority', $filters->leadPriority);
        }

        $wonProjects = (clone $comparisonQuery)->where('status', 'won')->count();
        $lostProjects = (clone $comparisonQuery)->where('status', 'lost')->count();
        $winRate = ($wonProjects + $lostProjects) > 0
            ? ($wonProjects / ($wonProjects + $lostProjects)) * 100
            : 0;

        return [
            'totalPipelineValue' => (clone $comparisonQuery)->sum('estimated_revenue'),
            'winRate' => $winRate,
        ];
    }

    private function calculateAverageSalesCycle(ReportFilterData $filters): int
    {
        $wonProjects = $this->buildBaseQuery($filters)
            ->where('status', 'won')
            ->whereNotNull('closed_at')
            ->get();

        if ($wonProjects->isEmpty()) {
            return 0;
        }

        $totalDays = $wonProjects->sum(fn ($project) => $project->created_at->diffInDays($project->closed_at));

        return (int) ($totalDays / $wonProjects->count());
    }

    private function getPipelineByStatus(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('status, COUNT(*) as count, SUM(estimated_revenue) as value')
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => ucfirst($row->status),
                'count' => $row->count,
                'value' => (float) $row->value,
            ]);
    }

    private function getPipelineBySalesRep(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->join('lead_collaborators', 'leads.id', '=', 'lead_collaborators.lead_id')
            ->join('users as collaborators', 'lead_collaborators.user_id', '=', 'collaborators.id')
            ->join('positions', 'collaborators.position_id', '=', 'positions.id')
            ->where('positions.name', 'not like', '%Director%')
            ->leftJoin('users as adders', 'lead_collaborators.added_by', '=', 'adders.id')
            ->selectRaw('
                collaborators.id as user_id,
                collaborators.name,
                COUNT(DISTINCT leads.id) as count,
                COALESCE(SUM(leads.estimated_revenue), 0) as value,
                GROUP_CONCAT(DISTINCT adders.name SEPARATOR ", ") as creator_names
            ')
            ->groupBy('collaborators.id', 'collaborators.name')
            ->orderByDesc('value')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'user_id' => $row->user_id,
                'name' => $row->name,
                'count' => (int) $row->count,
                'value' => (float) $row->value,
                'creator_name' => $row->creator_names,
            ]);
    }

    private function getMonthlyTrend(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(estimated_revenue) as value')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'period' => Carbon::create($row->year, $row->month)->format('M Y'),
                'count' => $row->count,
                'value' => (float) $row->value,
            ]);
    }

    private function getRecentWins(ReportFilterData $filters): Collection
    {
        return Lead::query()
            ->where('status', 'won')
            ->whereNotNull('closed_at')
            ->when($filters->userId, fn ($q) => $q->where('assigned_to', $filters->userId))
            ->when(! empty($filters->userIds), fn ($q) => $q->whereIn('assigned_to', $filters->userIds))
            ->when($filters->leadSource, fn ($q) => $q->where('source', $filters->leadSource))
            ->when($filters->leadPriority, fn ($q) => $q->where('priority', $filters->leadPriority))
            ->with(['customer:id,name', 'assignedUser:id,name'])
            ->orderByDesc('closed_at')
            ->limit(10)
            ->get()
            ->map(fn ($project) => [
                'id' => $project->id,
                'code' => $project->code,
                'name' => $project->name,
                'customer' => $project->customer?->name,
                'value' => (float) $project->estimated_revenue,
                'sales_rep' => $project->assignedUser?->name,
                'closed_at' => $project->closed_at?->format('d M Y'),
            ]);
    }

    private function getRecentLosses(ReportFilterData $filters): Collection
    {
        return Lead::query()
            ->where('status', 'lost')
            ->whereNotNull('closed_at')
            ->when($filters->userId, fn ($q) => $q->where('assigned_to', $filters->userId))
            ->when(! empty($filters->userIds), fn ($q) => $q->whereIn('assigned_to', $filters->userIds))
            ->when($filters->leadSource, fn ($q) => $q->where('source', $filters->leadSource))
            ->when($filters->leadPriority, fn ($q) => $q->where('priority', $filters->leadPriority))
            ->with(['customer:id,name', 'assignedUser:id,name'])
            ->orderByDesc('closed_at')
            ->limit(10)
            ->get()
            ->map(fn ($project) => [
                'id' => $project->id,
                'code' => $project->code,
                'name' => $project->name,
                'customer' => $project->customer?->name,
                'value' => (float) $project->estimated_revenue,
                'sales_rep' => $project->assignedUser?->name,
                'closed_at' => $project->closed_at?->format('d M Y'),
                'loss_reason' => $project->loss_reason,
            ]);
    }

    public function getExportData(ReportFilterData $filters): Collection
    {
        return $this->buildBaseQuery($filters)
            ->with(['customer:id,name', 'assignedUser:id,name'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($project) => [
                'Code' => $project->code,
                'Name' => $project->name,
                'Customer' => $project->customer?->name,
                'Sales Rep' => $project->assignedUser?->name,
                'Status' => ucfirst($project->status),
                'Estimated Value' => $project->estimated_revenue,
                'Confidence Level' => $project->confidence_level,
                'Created Date' => $project->created_at?->format('d M Y'),
                'Closed Date' => $project->closed_at?->format('d M Y'),
            ]);
    }
}
