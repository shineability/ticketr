<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCompleted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var \App\Order
     */
    private $order;

    /**
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(sprintf('%s order confirmation #%s', config('app.name'), $this->order->reference))
            ->markdown('email.order.completed')
            ->with(['order' => $this->order]);
    }
}
