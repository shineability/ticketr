<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCompleted
{
    public Order $order;

    use Dispatchable, SerializesModels;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
