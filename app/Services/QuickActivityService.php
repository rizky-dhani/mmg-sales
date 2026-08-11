<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QuickActivityService
{
    public function log(array $data, int $userId): Activity
    {
        $customer = Customer::find($data['customer_id']);

        if ($customer === null) {
            throw (new ModelNotFoundException)->setModel(Customer::class);
        }

        return Activity::create([
            'user_id' => $userId,
            'customer_id' => $customer->id,
            'type' => $data['type'],
            'subject' => $data['subject'],
            'description' => $data['notes'] ?? null,
            'performed_at' => $data['performed_at'] ?? now(),
        ]);
    }
}
