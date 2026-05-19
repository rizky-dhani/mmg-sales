<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use App\Models\Territory;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (empty($row['nama'])) {
                continue;
            }

            // Resolve department first so position can be linked to it
            $departmentId = $this->resolveDepartmentId($row['department'] ?? null);

            $user = User::create([
                'name' => $row['nama'],
                'email' => $row['email'] ?? null,
                'position_id' => $this->resolvePositionId($row['jabatan'] ?? null, $departmentId),
                'territory_id' => $this->resolveTerritoryId($row['area'] ?? null),
                'department_id' => $departmentId,
            ]);

            // Sync roles from the Roles column
            $this->syncRoles($user, $row['roles'] ?? null);
        }
    }

    private function resolvePositionId(mixed $value, ?int $departmentId): ?int
    {
        if (empty($value)) {
            return null;
        }

        $originalValue = trim((string) $value);
        $normalizedValue = $this->normalizeString($value);

        if (is_numeric($normalizedValue)) {
            return (int) $normalizedValue;
        }

        // First try to find existing position
        $position = Position::whereRaw('REPLACE(LOWER(name), ".", "") = ?', [$normalizedValue])->first();

        if ($position) {
            return $position->id;
        }

        // Create new position if not found (using original value for name)
        $position = Position::create([
            'name' => $originalValue,
            'level' => 0,
            'department_id' => $departmentId ?? $this->getDefaultDepartmentId(),
        ]);

        return $position->id;
    }

    private function resolveTerritoryId(mixed $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        $value = $this->normalizeString($value);

        if (is_numeric($value)) {
            return (int) $value;
        }

        $territory = Territory::whereRaw('REPLACE(LOWER(name), ".", "") = ?', [$value])->first();

        return $territory?->id;
    }

    private function resolveDepartmentId(mixed $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        $originalValue = trim((string) $value);
        $normalizedValue = $this->normalizeString($value);

        if (is_numeric($normalizedValue)) {
            return (int) $normalizedValue;
        }

        // Check by code first, then by name
        $department = Department::whereRaw('REPLACE(LOWER(code), ".", "") = ?', [$normalizedValue])
            ->orWhereRaw('REPLACE(LOWER(name), ".", "") = ?', [$normalizedValue])
            ->first();

        if ($department) {
            return $department->id;
        }

        // Create new department if not found (using original value for name)
        $department = Department::create([
            'name' => $originalValue,
        ]);

        return $department->id;
    }

    private function normalizeString(mixed $value): string
    {
        $value = trim((string) $value);
        $value = strtolower($value);
        $value = str_replace('.', '', $value);

        return $value;
    }

    private function getDefaultDepartmentId(): int
    {
        // Try to get the first existing department
        $department = Department::first();

        if ($department) {
            return $department->id;
        }

        // Create a default department if none exists
        $department = Department::create([
            'name' => 'General',
        ]);

        return $department->id;
    }

    private function syncRoles(User $user, mixed $rolesValue): void
    {
        if (empty($rolesValue)) {
            return;
        }

        // Handle comma-separated roles
        $roleNames = explode(',', (string) $rolesValue);
        $validRoles = [];

        foreach ($roleNames as $roleName) {
            $roleName = trim($roleName);
            if (empty($roleName)) {
                continue;
            }

            // Find existing role by name (case-insensitive)
            $role = Role::whereRaw('LOWER(name) = ?', [strtolower($roleName)])->first();

            if ($role) {
                $validRoles[] = $role->name;
            }
            // Skip unknown roles - don't create new ones
        }

        if (! empty($validRoles)) {
            $user->syncRoles($validRoles);
        }
    }
}
