<?php

use App\Models\Order;
use App\Models\Athlete;
use App\Models\User;

// Fix orphan orders
$orphans = Order::whereNull('athlete_id')->get();
$fixedCount = 0;

foreach ($orphans as $order) {
    $user = User::find($order->user_id);
    if ($user && $user->athlete_id) {
        $order->update(['athlete_id' => $user->athlete_id]);
        $fixedCount++;
    } elseif ($user && $user->athlete) {
        $order->update(['athlete_id' => $user->athlete->id]);
        $fixedCount++;
    }
}

echo "Fixed $fixedCount orphan orders.\n";
