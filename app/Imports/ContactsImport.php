<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection): void
    {
        foreach ($collection as $row) {
            if (empty($row['name'])) {
                continue;
            }

            $customerId = $this->resolveCustomerId($row['customer_id'] ?? $row['customer'] ?? null);

            if ($customerId === null) {
                continue;
            }

            $exists = Contact::query()
                ->where('customer_id', $customerId)
                ->where('name', $row['name'])
                ->exists();

            if ($exists) {
                continue;
            }

            Contact::create([
                'customer_id' => $customerId,
                'name' => $row['name'],
                'status' => $this->normalizeStatus($row['status'] ?? null),
                'position' => $row['position'] ?? null,
                'department' => $row['department'] ?? null,
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
                'mobile' => $row['mobile'] ?? null,
                'is_primary' => $this->normalizeBoolean($row['is_primary'] ?? null),
                'is_billing_contact' => $this->normalizeBoolean($row['is_billing_contact'] ?? null),
                'notes' => $row['notes'] ?? null,
            ]);
        }
    }

    /**
     * Normalize the status value to match the ENUM column ('active', 'inactive').
     */
    protected function normalizeStatus(?string $value): string
    {
        if (empty($value)) {
            return 'active';
        }

        $normalized = strtolower(trim($value));

        if (in_array($normalized, ['inactive', '0', 'no', 'false', 'n', 'tidak'], true)) {
            return 'inactive';
        }

        if (in_array($normalized, ['active', '1', 'yes', 'true', 'y', 'aktif'], true)) {
            return 'active';
        }

        return 'active';
    }

    /**
     * Normalize a value to boolean (1 or 0).
     */
    protected function normalizeBoolean(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'yes', 'true', 'y'], true);
    }

    /**
     * Resolve customer ID from value (ID, name, or code).
     */
    private function resolveCustomerId(?string $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return Customer::where('id', $value)->value('id');
        }

        $customer = Customer::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($value)])
            ->orWhereRaw('LOWER(customer_acc_code) = ?', [strtolower($value)])
            ->first();

        return $customer?->id;
    }
}
