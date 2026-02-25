<?php

use App\Models\Product;

it('has ecatalog_price field', function () {
    $product = Product::factory()->create([
        'unit_price' => 100000,
        'ecatalog_price' => 90000,
    ]);

    expect((float) $product->ecatalog_price)->toBe(90000.0);
    expect((float) $product->unit_price)->toBe(100000.0);
});

it('ecatalog_price can be null', function () {
    $product = Product::factory()->create([
        'unit_price' => 100000,
        'ecatalog_price' => null,
    ]);

    expect($product->ecatalog_price)->toBeNull();
});
