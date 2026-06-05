<?php

namespace App\DTOs;

use Carbon\Carbon;

readonly class ReportFilterData
{
    public function __construct(
        public Carbon $startDate,
        public Carbon $endDate,
        public ?Carbon $comparisonStartDate = null,
        public ?Carbon $comparisonEndDate = null,
        public ?int $userId = null,
        public ?int $territoryId = null,
        public ?int $departmentId = null,
        public ?int $principalId = null,
        public ?int $distributorId = null,
        public ?int $customerId = null,
        public ?int $customerGroupId = null,
        public ?int $segmentId = null,
        public ?int $subSegmentId = null,
        public ?int $salesTypeId = null,
        public ?int $itemId = null,
        public ?int $projectId = null,
        public ?string $orderStatus = null,
        public ?string $paymentStatus = null,
        public ?string $projectStatus = null,
        public ?string $projectSource = null,
        public ?string $projectPriority = null,
        public ?string $cdNcdType = null,
        public array $userIds = [],
    ) {}

    public function hasComparison(): bool
    {
        return $this->comparisonStartDate !== null && $this->comparisonEndDate !== null;
    }

    public function getPrimaryDateRangeLabel(): string
    {
        return "{$this->startDate->format('d M Y')} - {$this->endDate->format('d M Y')}";
    }

    public function getComparisonDateRangeLabel(): string
    {
        if (! $this->hasComparison()) {
            return '';
        }

        return "{$this->comparisonStartDate->format('d M Y')} - {$this->comparisonEndDate->format('d M Y')}";
    }

    public function toCacheKey(): string
    {
        $parts = [
            $this->startDate->format('Y-m-d'),
            $this->endDate->format('Y-m-d'),
            $this->comparisonStartDate?->format('Y-m-d'),
            $this->comparisonEndDate?->format('Y-m-d'),
            $this->userId,
            $this->territoryId,
            $this->departmentId,
            $this->principalId,
            $this->distributorId,
            $this->customerId,
            $this->customerGroupId,
            $this->segmentId,
            $this->subSegmentId,
            $this->salesTypeId,
            $this->itemId,
            $this->projectId,
            $this->orderStatus,
            $this->paymentStatus,
            $this->projectStatus,
            $this->projectSource,
            $this->projectPriority,
            $this->cdNcdType,
            implode('-', $this->userIds),
        ];

        return md5(implode('|', $parts));
    }

    public static function fromArray(array $data): self
    {
        $startDate = Carbon::parse($data['start_date'] ?? now()->startOfYear());
        $endDate = Carbon::parse($data['end_date'] ?? now()->endOfYear());

        $comparisonStartDate = isset($data['comparison_start_date'])
            ? Carbon::parse($data['comparison_start_date'])
            : null;
        $comparisonEndDate = isset($data['comparison_end_date'])
            ? Carbon::parse($data['comparison_end_date'])
            : null;

        return new self(
            startDate: $startDate,
            endDate: $endDate,
            comparisonStartDate: $comparisonStartDate,
            comparisonEndDate: $comparisonEndDate,
            userId: $data['user_id'] ?? null,
            territoryId: $data['territory_id'] ?? null,
            departmentId: $data['department_id'] ?? null,
            principalId: $data['principal_id'] ?? null,
            distributorId: $data['distributor_id'] ?? null,
            customerId: $data['customer_id'] ?? null,
            customerGroupId: $data['customer_group_id'] ?? null,
            segmentId: $data['segment_id'] ?? null,
            subSegmentId: $data['sub_segment_id'] ?? null,
            salesTypeId: $data['sales_type_id'] ?? null,
            itemId: $data['item_id'] ?? null,
            projectId: $data['project_id'] ?? null,
            orderStatus: $data['order_status'] ?? null,
            paymentStatus: $data['payment_status'] ?? null,
            projectStatus: $data['project_status'] ?? null,
            projectSource: $data['project_source'] ?? null,
            projectPriority: $data['project_priority'] ?? null,
            cdNcdType: $data['cd_ncd_type'] ?? null,
            userIds: $data['user_ids'] ?? [],
        );
    }
}
