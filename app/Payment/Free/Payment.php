<?php

namespace App\Payment\Free;

use App\Models\Order;
use App\Payment\Contracts\Payment as PaymentContract;

final class Payment implements PaymentContract
{
    /**
     * Create a new payment instance.
     */
    private function __construct(
        private Order $order,
        private string $status
    ) {}

    /**
     * Create a completed payment.
     */
    public static function completed(Order $order): self
    {
        return new self($order, 'completed');
    }

    /**
     * Create a canceled payment.
     */
    public static function canceled(Order $order): self
    {
        return new self($order, 'canceled');
    }

    /**
     * Check if the payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status() === 'completed';
    }

    /**
     * Check if the payment is canceled.
     */
    public function isCanceled(): bool
    {
        return $this->status() === 'canceled';
    }

    /**
     * Get the payment status.
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * Get the order ID.
     */
    public function orderId(): string
    {
        return $this->order->uuid;
    }

    /**
     * Get the transaction ID.
     */
    public function transactionId(): string
    {
        return $this->orderId();
    }
}
