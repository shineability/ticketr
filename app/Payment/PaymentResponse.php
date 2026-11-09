<?php

namespace App\Payment;

use App\Payment\Contracts\Payment;

final class PaymentResponse
{
    /**
     * Create a new payment response.
     */
    private function __construct(
        private Payment $payment,
        private string $checkoutUrl
    ) {}

    /**
     * Create a new payment response instance.
     */
    public static function make(Payment $payment, string $checkoutUrl): self
    {
        return new self($payment, $checkoutUrl);
    }

    /**
     * Get the payment.
     */
    public function payment(): Payment
    {
        return $this->payment;
    }

    /**
     * Get the checkout URL.
     */
    public function checkoutUrl(): string
    {
        return $this->checkoutUrl;
    }
}
