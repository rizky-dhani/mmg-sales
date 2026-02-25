<?php

use App\Models\Item;

it('has ecatalog_price field', function () {
    $item = Item::factory()->create([
        'unit_price' => 100000,
        'ecatalog_price' => 90000,
    ]);

    expect((float) $item->ecatalog_price)->toBe(90000.0);
    expect((float) $item->unit_price)->toBe(100000.0);
});

it('ecatalog_price can be null', function () {
    $item = Item::factory()->create([
        'unit_price' => 100000,
        'ecatalog_price' => null,
    ]);

    expect($item->ecatalog_price)->toBeNull();
});
