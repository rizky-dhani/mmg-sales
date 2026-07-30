<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Segment;
use App\Models\SubSegment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersSheetImport implements ToCollection, WithHeadingRow
{
    /**
     * Allowed values for the customer type ENUM column.
     */
    protected const ALLOWED_TYPES = [
        'hospital_clinic',
        'pt_cv',
        'other',
    ];

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (empty($row['name'])) {
                continue;
            }

            $customerGroupId = $this->resolveCustomerGroupId($row['customer_group'] ?? $row['customer_group_id'] ?? null);
            $segmentId = $this->resolveSegmentId($row['segment'] ?? null);
            $subSegmentId = $this->resolveSubSegmentId($row['sub_segment'] ?? null, $segmentId);
            $typeData = $this->normalizeType($row['type'] ?? null);
            $isActive = $this->normalizeIsActive($row['is_active'] ?? null);
            $status = $this->normalizeStatus($row['status'] ?? null);
            $cdNcdType = $this->normalizeCdNcdType($row['cd_ncd_type'] ?? null);
            $customerAccCode = $row['internal_code'] ?? null;

            $existing = Customer::query()
                ->where(function ($q) use ($customerAccCode, $row) {
                    if (! empty($customerAccCode)) {
                        $q->where('internal_code', $customerAccCode);
                    }
                    if (! empty($row['name'])) {
                        $q->orWhere('name', $row['name']);
                    }
                    if (! empty($row['email'])) {
                        $q->orWhere('email', $row['email']);
                    }
                    if (! empty($row['phone'])) {
                        $q->orWhere('phone', $row['phone']);
                    }
                })
                ->first();

            $data = [
                'name' => $row['name'],
                'customer_name' => $row['customer_name'] ?? $row['name'],
                'type' => $typeData['type'],
                'other_type' => $typeData['other_type'] ?? $row['other_type'] ?? null,
                'tax_number' => $row['tax_number'] ?? null,
                'address' => $row['address'] ?? null,
                'city' => $row['city'] ?? null,
                'state' => $row['state'] ?? null,
                'postal_code' => $row['postal_code'] ?? null,
                'country' => $row['country'] ?? 'Indonesia',
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
                'website' => $row['website'] ?? null,
                'is_active' => $isActive,
                'status' => $status,
                'cd_ncd_type' => $cdNcdType,
                'segment_id' => $segmentId,
                'sub_segment_id' => $subSegmentId,
                'customer_group_id' => $customerGroupId,
                'internal_code' => $customerAccCode,
                'payment_terms' => $row['payment_terms'] ?? null,
            ];

            if ($existing) {
                $existing->update($data);
            } else {
                Customer::create($data);
            }
        }
    }

    /**
     * Normalize the type value to match the ENUM column.
     * If the type doesn't match allowed values, sets type to 'other'
     * and stores the original value in other_type.
     */
    protected function normalizeType(?string $type): array
    {
        if (empty($type)) {
            return ['type' => 'other', 'other_type' => null];
        }

        // Trim whitespace and convert to lowercase for comparison
        $normalizedType = strtolower(trim($type));

        // Check if it matches an allowed type
        if (in_array($normalizedType, self::ALLOWED_TYPES, true)) {
            return ['type' => $normalizedType, 'other_type' => null];
        }

        // Store custom type in other_type and default to 'other'
        return ['type' => 'other', 'other_type' => $type];
    }

    /**
     * Normalize the is_active value to integer (0 or 1).
     * Handles string values like 'Active', 'Inactive', 'Yes', 'No', etc.
     */
    protected function normalizeIsActive(mixed $value): int
    {
        if ($value === null) {
            return 1; // Default to active
        }

        // If already an integer, return it
        if (is_int($value)) {
            return $value ? 1 : 0;
        }

        // Convert to string and normalize
        $normalizedValue = strtolower(trim((string) $value));

        // Active states
        $activeStates = ['active', 'yes', '1', 'true', 'y', 'aktif', '1.0'];
        // Inactive states
        $inactiveStates = ['inactive', 'no', '0', 'false', 'n', 'tidak', '0.0'];

        if (in_array($normalizedValue, $inactiveStates, true)) {
            return 0;
        }

        if (in_array($normalizedValue, $activeStates, true)) {
            return 1;
        }

        // Try to parse as integer
        $intValue = filter_var($normalizedValue, FILTER_VALIDATE_INT);
        if ($intValue !== false) {
            return $intValue ? 1 : 0;
        }

        // Default to active if unrecognized
        return 1;
    }

    /**
     * Normalize the status value to match the ENUM column ('active', 'inactive').
     * Handles common variations from imports.
     */
    protected function normalizeStatus(?string $value): string
    {
        if (empty($value)) {
            return 'active'; // Default to active
        }

        $normalized = strtolower(trim($value));

        $activeStates = ['active', '1', 'yes', 'true', 'y', 'aktif'];
        $passiveStates = ['passive', 'inactive', '0', 'no', 'false', 'n', 'tidak'];

        if (in_array($normalized, $passiveStates, true)) {
            return 'inactive';
        }

        if (in_array($normalized, $activeStates, true)) {
            return 'active';
        }

        // Try to parse as integer (truthy = active, falsy = inactive)
        $intValue = filter_var($normalized, FILTER_VALIDATE_INT);
        if ($intValue !== false) {
            return $intValue ? 'active' : 'inactive';
        }

        return 'active';
    }

    /**
     * Normalize the cd_ncd_type value to match the ENUM column ('CD', 'LS').
     * Extracts the type from descriptive values like 'LS_LIFE SCIENCE (LS)'.
     */
    protected function normalizeCdNcdType(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $trimmed = trim($value);
        $upper = strtoupper($trimmed);

        // Direct match
        if (in_array($upper, ['CD', 'LS', 'NCD', 'N-CD'], true)) {
            // Normalize legacy NCD values to LS
            return in_array($upper, ['NCD', 'N-CD'], true) ? 'LS' : $upper;
        }

        // Extract from parentheses, e.g. 'LS_LIFE SCIENCE (LS)' -> 'LS'
        if (preg_match('/\(([^)]+)\)/', $trimmed, $matches)) {
            $extracted = strtoupper(str_replace('-', '', trim($matches[1])));
            if (in_array($extracted, ['CD', 'LS', 'NCD'], true)) {
                return in_array($extracted, ['NCD'], true) ? 'LS' : $extracted;
            }
        }

        // Check if the string contains LS or NCD
        if (str_contains($upper, 'LS') || str_contains($upper, 'NCD') || str_contains($upper, 'N-CD')) {
            return 'LS';
        }

        if (str_contains($upper, 'CD')) {
            return 'CD';
        }

        return null;
    }

    /**
     * Resolve segment ID from name (likeness match).
     * Returns null if not found.
     */
    private function resolveSegmentId(?string $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        $trimmed = trim($value);

        // If numeric, treat as ID
        if (is_numeric($trimmed)) {
            return Segment::where('id', $trimmed)->value('id');
        }

        // Exact match first
        $segment = Segment::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($trimmed)])
            ->orWhereRaw('LOWER(code) = ?', [strtolower($trimmed)])
            ->first();

        if ($segment) {
            return $segment->id;
        }

        // Likeness match (contains)
        $segment = Segment::query()
            ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($trimmed) . '%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower(addslashes($trimmed)) . '%'])
            ->first();

        return $segment?->id;
    }

    /**
     * Resolve sub-segment ID from name (likeness match).
     * Returns null if not found.
     */
    private function resolveSubSegmentId(?string $value, ?int $segmentId): ?int
    {
        if (empty($value)) {
            return null;
        }

        $trimmed = trim($value);

        // If numeric, treat as ID
        if (is_numeric($trimmed)) {
            return SubSegment::where('id', $trimmed)->value('id');
        }

        $query = SubSegment::query();
        if ($segmentId) {
            $query->where('segment_id', $segmentId);
        }

        // Exact match first
        $subSegment = $query
            ->whereRaw('LOWER(name) = ?', [strtolower($trimmed)])
            ->orWhereRaw('LOWER(code) = ?', [strtolower($trimmed)])
            ->first();

        if ($subSegment) {
            return $subSegment->id;
        }

        // Likeness match (contains)
        $query2 = SubSegment::query();
        if ($segmentId) {
            $query2->where('segment_id', $segmentId);
        }

        $subSegment = $query2
            ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($trimmed) . '%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower(addslashes($trimmed)) . '%'])
            ->first();

        return $subSegment?->id;
    }

    /**
     * Resolve customer group ID from value (ID, name, or code).
     * Returns null if not found.
     */
    private function resolveCustomerGroupId(?string $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        // If numeric, treat as ID
        if (is_numeric($value)) {
            return CustomerGroup::where('id', $value)->value('id');
        }

        // Try to find by name or code (case-insensitive)
        $group = CustomerGroup::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($value)])
            ->orWhereRaw('LOWER(code) = ?', [strtolower($value)])
            ->first();

        return $group?->id;
    }
}
