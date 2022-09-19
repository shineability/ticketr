<?php

namespace App\Payment\Free;

use App\Models\Order;
use App\Payment\Contracts\Payment as PaymentContract;

final class Payment implements PaymentContract
{
    private Order $order;
    private string $status;

    private function __construct(Order $order, string $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public static function completed(Order $order)
    {
        return new self($order, 'completed');
    }

    public static function canceled(Order $order)
    {
        return new self($order, 'canceled');
    }

    public function isCompleted(): bool
    {
        return $this->status() === 'completed';
    }

    public function isCanceled(): bool
    {
        return $this->status() === 'canceled';
    }

    public function status(): string
    {
        return $this->status;
    }

    public function orderId(): string
    {
        return $this->order->uuid;
    }

    public function transactionId(): string
    {
        return $this->orderId();
    }
}
