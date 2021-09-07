<?php

namespace App\Payment\Stripe;

use App\Payment\Contracts\Payment as PaymentContract;
use Stripe\Checkout\Session as StripeCheckoutSession;

final class Payment implements PaymentContract
{
    /**
     * @param StripeCheckoutSession $session
     */
    private $session;

    public function __construct(StripeCheckoutSession $session)
    {
        $this->session = $session;
    }

    public function isCompleted(): bool
    {
        return $this->getStatus() == 'paid';
    }

    public function isCanceled(): bool
    {
        return false;
    }

    public function getStatus(): string
    {
        return $this->session->payment_status;
    }

    public function getOrderId(): string
    {
        return $this->session->metadata->order_id;
    }

    public function getTransactionId(): string
    {
        return $this->session->id;
    }
}