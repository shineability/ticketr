<?php

namespace App\Payment\Mollie;

use App\Payment\Contracts\Payment as PaymentContract;
use Mollie\Api\Resources\Payment as MolliePayment;

final class Payment implements PaymentContract
{
    /**
     * @param MolliePayment $payment
     */
    private $payment;

    public function __construct(MolliePayment $payment)
    {
        $this->payment = $payment;
    }

    public function isCompleted(): bool
    {
        return in_array($this->getStatus(), ['paid', 'authorized']);
    }

    public function isCanceled(): bool
    {
        return $this->getStatus() === 'canceled';
    }

    public function getStatus(): string
    {
        return $this->payment->status;
    }

    public function getOrderId(): string
    {
        return $this->payment->metadata->order_id;
    }

    public function getTransactionId(): string
    {
        return $this->payment->id;
    }
}
