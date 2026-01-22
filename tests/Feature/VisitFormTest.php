<?php

use App\Filament\Resources\Visits\Pages\CreateVisit;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('can create a visit with new fields', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $this->actingAs($user);

    $customer = Customer::factory()->create();

    Livewire::test(CreateVisit::class)
        ->set('data.customer_id', $customer->id)
        ->set('data.visit_type', 'Video Call')
        ->set('data.meeting_link', 'https://zoom.us/j/123456')
        ->set('data.confidence_level', 85)
        ->set('data.purpose', 'Test Purpose')
        ->set('data.expectations', 'Test Expectations')
        ->set('data.targets', 'Test Targets')
        ->set('data.visit_started_at', now()->toDateTimeString())
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('visits', [
        'customer_id' => $customer->id,
        'visit_type' => 'Video Call',
        'meeting_link' => 'https://zoom.us/j/123456',
        'confidence_level' => 85,
    ]);
});

it('hides meeting link when visit type is not video call', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $this->actingAs($user);

    Livewire::test(CreateVisit::class)
        ->set('data.visit_type', 'In-person')
        ->assertFormFieldIsHidden('meeting_link')
        ->set('data.visit_type', 'Video Call')
        ->assertFormFieldIsVisible('meeting_link');
});

it('hides messaging platform when visit type is not messaging', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    $this->actingAs($user);

    Livewire::test(CreateVisit::class)
        ->set('data.visit_type', 'In-person')
        ->assertFormFieldIsHidden('messaging_platform')
        ->set('data.visit_type', 'Messaging')
        ->assertFormFieldIsVisible('messaging_platform');
});
