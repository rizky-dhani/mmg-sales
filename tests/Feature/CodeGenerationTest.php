<?php

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Principal;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::table('code_sequences')->truncate();
});

describe('Model Code Generation', function () {
    it('generates project_code when creating project', function () {
        $project = Project::factory()->create();

        expect($project->project_code)->not->toBeNull();
        expect($project->project_code)->toMatch('/^MMG-PRJ-\d{6}$/');
    });

    it('generates activity_code when creating activity', function () {
        $activity = Activity::factory()->create();

        expect($activity->activity_code)->not->toBeNull();
        expect($activity->activity_code)->toMatch('/^MMG-ACT-\d{6}$/');
    });

    it('generates customer_code when creating customer', function () {
        $customer = Customer::factory()->create();

        expect($customer->customer_code)->not->toBeNull();
        expect($customer->customer_code)->toMatch('/^MMG-CST-\d{6}$/');
    });

    it('generates contact_code when creating contact', function () {
        $contact = Contact::factory()->create();

        expect($contact->contact_code)->not->toBeNull();
        expect($contact->contact_code)->toMatch('/^MMG-CON-\d{6}$/');
    });

    it('generates principal_code when creating principal', function () {
        $principal = Principal::factory()->create();

        expect($principal->principal_code)->not->toBeNull();
        expect($principal->principal_code)->toMatch('/^MMG-PRN-\d{6}$/');
    });

    it('generates product_code when creating product', function () {
        $product = Product::factory()->create();

        expect($product->product_code)->not->toBeNull();
        expect($product->product_code)->toMatch('/^MMG-PRO-\d{6}$/');
    });

    it('generates sequential codes for multiple projects', function () {
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        $project3 = Project::factory()->create();

        expect($project1->project_code)->toBe('MMG-PRJ-000001');
        expect($project2->project_code)->toBe('MMG-PRJ-000002');
        expect($project3->project_code)->toBe('MMG-PRJ-000003');
    });

    it('allows manual code assignment', function () {
        $project = Project::factory()->create([
            'project_code' => 'CUSTOM-001',
        ]);

        expect($project->project_code)->toBe('CUSTOM-001');
    });

    it('generates unique codes across different models', function () {
        $project = Project::factory()->create();
        $activity = Activity::factory()->create();
        $customer = Customer::factory()->create();

        expect($project->project_code)->toMatch('/^MMG-PRJ-\d{6}$/');
        expect($activity->activity_code)->toMatch('/^MMG-ACT-\d{6}$/');
        expect($customer->customer_code)->toMatch('/^MMG-CST-\d{6}$/');
    });
});

describe('Order Order Number Generation', function () {
    it('generates order_number when creating order', function () {
        $order = Order::factory()->create([
            'tahun' => 2025,
        ]);

        expect($order->order_number)->not->toBeNull();
        expect($order->order_number)->toMatch('/^MMG-ORD-2025-\d{6}$/');
    });

    it('generates sequential order numbers', function () {
        $order1 = Order::factory()->create(['tahun' => 2025]);
        $order2 = Order::factory()->create(['tahun' => 2025]);
        $order3 = Order::factory()->create(['tahun' => 2025]);

        expect($order1->order_number)->toBe('MMG-ORD-2025-000001');
        expect($order2->order_number)->toBe('MMG-ORD-2025-000002');
        expect($order3->order_number)->toBe('MMG-ORD-2025-000003');
    });

    it('resets order sequence for new year', function () {
        Order::factory()->create(['tahun' => 2025]);
        Order::factory()->create(['tahun' => 2025]);
        $order2026 = Order::factory()->create(['tahun' => 2026]);

        expect($order2026->order_number)->toBe('MMG-ORD-2026-000001');
    });

    it('generates order number using current year when not specified', function () {
        $order = Order::factory()->create();

        expect($order->order_number)->not->toBeNull();
        expect($order->order_number)->toMatch('/^MMG-ORD-'.date('Y').'-\d{6}$/');
    });
});
