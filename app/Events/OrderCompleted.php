<?php

namespace App\Events;

use App\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCompleted
{
    /**
     * @var \App\Order
     */
    public $order;

    use Dispatchable, SerializesModels;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
