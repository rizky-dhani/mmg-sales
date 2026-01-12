<?php

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('order has order items relationship', function () {
    $order = new Order();
    
    expect($order->orderItems())->toBeInstanceOf(HasMany::class);
});

test('order item has order relationship', function () {
    $orderItem = new OrderItem();
    
    expect($orderItem->order())->toBeInstanceOf(BelongsTo::class);
});