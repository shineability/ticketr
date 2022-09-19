<?php

namespace App\Payment\Stripe;

use App\Payment\Contracts\Payment as PaymentContract;
use Stripe\Checkout\Session as StripeCheckoutSession;

final class Payment implements PaymentContract
{
    private StripeCheckoutSession $session;

    public function __construct(StripeCheckoutSession $session)
    {
        $this->session = $session;
    }

    public function isCompleted(): bool
    {
        return $this->status() == 'paid';
    }

    public function isCanceled(): bool
    {
        return false;
    }

    public function status(): string
    {
        return $this->session->payment_status;
    }

    public function orderId(): string
    {
        return $this->session->metadata->order_id;
    }

    public function transactionId(): string
    {
        return $this->session->id;
    }
}
