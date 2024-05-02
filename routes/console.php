<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('assign', function () {
    $orders = \App\Models\Order::whereNull('user_id')->get();
    foreach ($orders as $order) {
        // must be user have less than 10 orders
        $user = \App\Models\User::where('auto_assign', true)
            ->where('role', 1)
            ->withCount(['orders' => function ($query) {
                $query->where('status', \App\Enums\OrderStatusEnum::NEW);
            }])
            ->having('orders_count', '<', 10)
            ->orderBy('orders_count')
            ->first();
        $order->user_id = $user->id;
        $order->save();
    }
    $this->info('Orders assigned successfully');
})
->purpose('Assign orders to users')
->withoutOverlapping();
