<?php

namespace App\Payment\Stripe;

use App\Payment\Contracts\Payment as PaymentContract;
use Stripe\Checkout\Session as StripeCheckoutSession;

final class Payment implements PaymentContract
{
    /**
     * Create a new payment instance.
     */
    public function __construct(
        private StripeCheckoutSession $session
    ) {}

    /**
     * Check if the payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status() === 'paid';
    }

    /**
     * Check if the payment is canceled.
     *
     * Note: Stripe checkout sessions don't have a canceled status in the same way
     * as other payment providers, so this always returns false.
     */
    public function isCanceled(): bool
    {
        return false;
    }

    /**
     * Get the payment status.
     */
    public function status(): string
    {
        return $this->session->payment_status;
    }

    /**
     * Get the transaction ID.
     */
    public function transactionId(): string
    {
        return $this->session->id;
    }
}
