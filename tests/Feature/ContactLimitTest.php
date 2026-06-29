<?php

use App\Models\Contact;
use App\Models\Customer;

test('contact creation is blocked when limit is reached', function () {
    $customer = Customer::factory()->create(['max_contact_persons' => 2]);
    Contact::factory()->create(['customer_id' => $customer->id]);
    Contact::factory()->create(['customer_id' => $customer->id]);

    $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    Contact::factory()->create(['customer_id' => $customer->id]);
});

test('contact creation succeeds when under limit', function () {
    $customer = Customer::factory()->create(['max_contact_persons' => 3]);
    Contact::factory()->create(['customer_id' => $customer->id]);

    $contact = Contact::factory()->create(['customer_id' => $customer->id]);
    expect($contact->fresh())->not->toBeNull();
});

test('unlimited contacts when max_contact_persons is null', function () {
    $customer = Customer::factory()->create(['max_contact_persons' => null]);

    foreach (range(1, 10) as $i) {
        Contact::factory()->create(['customer_id' => $customer->id]);
    }

    expect($customer->contacts()->count())->toBe(10);
});

test('trashed contacts do not count toward limit', function () {
    $customer = Customer::factory()->create(['max_contact_persons' => 2]);
    $contact1 = Contact::factory()->create(['customer_id' => $customer->id]);
    Contact::factory()->create(['customer_id' => $customer->id]);

    $contact1->delete();

    $newContact = Contact::factory()->create(['customer_id' => $customer->id]);
    expect($newContact->fresh())->not->toBeNull();
});

test('contact creation is blocked for inactive customer', function () {
    $customer = Customer::factory()->create(['status' => 'inactive']);

    $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    Contact::factory()->create(['customer_id' => $customer->id]);
});
