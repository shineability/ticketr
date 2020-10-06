<?php

namespace App\Payment\Free;

use App\Order;
use App\Payment\Contracts\Payment as PaymentContract;

final class Payment implements PaymentContract
{
    /**
     * @var \App\Order
     */
    private $order;

    /**
     * @var string
     */
    private $status;

    private function __construct(Order $order, string $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public static function completed(Order $order)
    {
        return new static($order, 'completed');
    }

    public static function canceled(Order $order)
    {
        return new static($order, 'canceled');
    }

    public function isCompleted(): bool
    {
        return $this->getStatus() === 'completed';
    }

    public function isCanceled(): bool
    {
        return $this->getStatus() === 'canceled';
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getOrderId(): string
    {
        return $this->order->uuid;
    }

    public function getTransactionId(): string
    {
        return $this->getOrderId();
    }
}
