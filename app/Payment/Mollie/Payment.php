<?php

namespace App\Payment\Mollie;

use App\Payment\Contracts\Payment as PaymentContract;
use Mollie\Api\Resources\Payment as MolliePayment;

final class Payment implements PaymentContract
{
    private MolliePayment $payment;

    public function __construct(MolliePayment $payment)
    {
        $this->payment = $payment;
    }

    public function isCompleted(): bool
    {
        return in_array($this->status(), ['paid', 'authorized']);
    }

    public function isCanceled(): bool
    {
        return $this->status() === 'canceled';
    }

    public function status(): string
    {
        return $this->payment->status;
    }

    public function orderId(): string
    {
        return $this->payment->metadata->order_id;
    }

    public function transactionId(): string
    {
        return $this->payment->id;
    }
}
