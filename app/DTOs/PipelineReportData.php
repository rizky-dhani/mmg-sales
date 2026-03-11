<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

readonly class PipelineReportData
{
    public function __construct(
        public float $totalPipelineValue,
        public float $wonValue,
        public float $lostValue,
        public int $totalProjects,
        public int $wonProjects,
        public int $lostProjects,
        public float $winRate,
        public float $averageDealSize,
        public int $averageSalesCycle,
        public Collection $pipelineByStatus,
        public Collection $pipelineBySalesRep,
        public Collection $monthlyTrend,
        public Collection $recentWins,
        public Collection $recentLosses,
        public ?float $comparisonTotalPipelineValue = null,
        public ?float $comparisonWinRate = null,
    ) {}

    public function getPipelineGrowthPercentage(): ?float
    {
        if ($this->comparisonTotalPipelineValue === null || $this->comparisonTotalPipelineValue == 0) {
            return null;
        }

        return (($this->totalPipelineValue - $this->comparisonTotalPipelineValue) / $this->comparisonTotalPipelineValue) * 100;
    }

    public function getWinRateChange(): ?float
    {
        if ($this->comparisonWinRate === null) {
            return null;
        }

        return $this->winRate - $this->comparisonWinRate;
    }
}
