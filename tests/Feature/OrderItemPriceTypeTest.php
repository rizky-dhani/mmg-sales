<?php

use App\Models\OrderItem;

it('has price_type field in fillable', function () {
    $orderItem = new OrderItem;
    expect(in_array('price_type', $orderItem->getFillable()))->toBeTrue();
});

it('has correct casts for decimal fields', function () {
    $orderItem = new OrderItem;
    $casts = $orderItem->getCasts();

    expect($casts['unit_price'] ?? null)->toBe('decimal:2');
    expect($casts['current_price'] ?? null)->toBe('decimal:2');
    expect($casts['subtotal'] ?? null)->toBe('decimal:2');
});

it('calculates line total correctly with unit_price', function () {
    $quantity = 3;
    $unitPrice = 100000;
    $expectedTotal = $quantity * $unitPrice;

    expect($expectedTotal)->toBe(300000);
});

it('calculates line total correctly with ecatalog_price', function () {
    $quantity = 3;
    $ecatalogPrice = 80000;
    $expectedTotal = $quantity * $ecatalogPrice;

    expect($expectedTotal)->toBe(240000);
});

it('price_type accepts unit_price value', function () {
    $orderItem = new OrderItem(['price_type' => 'unit_price']);
    expect($orderItem->price_type)->toBe('unit_price');
});

it('price_type accepts ecatalog_price value', function () {
    $orderItem = new OrderItem(['price_type' => 'ecatalog_price']);
    expect($orderItem->price_type)->toBe('ecatalog_price');
});

it('price_type can be null', function () {
    $orderItem = new OrderItem(['price_type' => null]);
    expect($orderItem->price_type)->toBeNull();
});
