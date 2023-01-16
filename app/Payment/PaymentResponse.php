<?php

namespace App\Payment;

use App\Payment\Contracts\Payment;

final class PaymentResponse
{
    private Payment $payment;

    private string $checkoutUrl;

    private function __construct(Payment $payment, string $checkoutUrl)
    {
        $this->payment = $payment;
        $this->checkoutUrl = $checkoutUrl;
    }

    public static function make(Payment $payment, string $checkoutUrl): self
    {
        return new static($payment, $checkoutUrl);
    }

    public function payment(): Payment
    {
        return $this->payment;
    }

    public function checkoutUrl(): string
    {
        return $this->checkoutUrl;
    }
}
