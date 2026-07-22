<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactsSheetImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;

    public function collection(Collection $collection): void
    {
        $first = true;
        foreach ($collection as $i => $row) {
            if ($first) {
                logger("ContactsImport: first row keys: ".json_encode(array_keys($row->toArray()))." values: ".json_encode($row->toArray()));
                $first = false;
            }

            if (empty($row['name'])) {
                continue;
            }

            $customerId = $this->resolveCustomerId($row['customer'] ?? $row['customer_id'] ?? null);

            if ($customerId === null) {
                logger("ContactsImport: row {$i} skipped — customer not resolved, customer value: ".json_encode($row['customer'] ?? $row['customer_id'] ?? null));
                continue;
            }

            $phone = $row['phone'] ?? null;
            $mobile = $row['mobile'] ?? null;
            $email = $row['email'] ?? null;

            if (!empty($phone) || !empty($mobile) || !empty($email)) {
                $exists = Contact::query()
                    ->where('customer_id', $customerId)
                    ->where(function ($q) use ($phone, $mobile, $email) {
                        if (!empty($phone)) {
                            $q->orWhere('phone', $phone);
                        }
                        if (!empty($mobile)) {
                            $q->orWhere('mobile', $mobile);
                        }
                        if (!empty($email)) {
                            $q->orWhere('email', $email);
                        }
                    })
                    ->exists();

                if ($exists) {
                    continue;
                }
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
            ]);

            $this->importedCount++;
        }
    }

    /**
     * Normalize the status value to match the ENUM column ('active', 'inactive').
     */
    protected function normalizeStatus(mixed $value): string
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return 'active';
        }

        $normalized = strtolower(trim((string) $value));

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
    private function resolveCustomerId(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }

        $value = trim((string) $value);

        if (is_numeric($value)) {
            $id = Customer::where('id', (int) $value)->value('id');
            if ($id !== null) {
                return $id;
            }
        }

        $customer = Customer::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($value)])
            ->orWhereRaw('LOWER(TRIM(customer_acc_code)) = ?', [strtolower($value)])
            ->first();

        return $customer?->id;
    }
}
