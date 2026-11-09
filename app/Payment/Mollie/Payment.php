<?php

namespace App\Payment\Mollie;

use App\Payment\Contracts\Payment as PaymentContract;
use Mollie\Api\Resources\Payment as MolliePayment;

final class Payment implements PaymentContract
{
    /**
     * Create a new payment instance.
     */
    public function __construct(
        private MolliePayment $payment
    ) {}

    /**
     * Check if the payment is completed.
     */
    public function isCompleted(): bool
    {
        return in_array($this->status(), ['paid', 'authorized']);
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
        return $this->payment->status;
    }

    /**
     * Get the transaction ID.
     */
    public function transactionId(): string
    {
        return $this->payment->id;
    }
}
