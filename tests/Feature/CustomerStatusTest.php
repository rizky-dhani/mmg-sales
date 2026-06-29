<?php

use App\Models\Contact;
use App\Models\Customer;

test('setting customer to inactive deactivates all active contacts', function () {
    $customer = Customer::factory()->create(['status' => 'active']);
    $contact1 = Contact::factory()->create(['customer_id' => $customer->id, 'status' => 'active']);
    $contact2 = Contact::factory()->create(['customer_id' => $customer->id, 'status' => 'active']);

    $customer->update(['status' => 'inactive']);

    expect($contact1->fresh()->status)->toBe('inactive');
    expect($contact2->fresh()->status)->toBe('inactive');
});

test('setting customer to active does not change contact status', function () {
    $customer = Customer::factory()->create(['status' => 'inactive']);
    $contact = Contact::factory()->create(['customer_id' => $customer->id, 'status' => 'inactive']);

    $customer->update(['status' => 'active']);

    expect($contact->fresh()->status)->toBe('inactive');
});

test('customer status accepts active and inactive values', function () {
    $customer = Customer::factory()->create(['status' => 'active']);
    expect($customer->status)->toBe('active');

    $customer->update(['status' => 'inactive']);
    expect($customer->fresh()->status)->toBe('inactive');
});
