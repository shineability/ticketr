<?php

namespace App\Payment;

use App\Payment\Contracts\Payment;

final class PaymentResponse
{
    /**
     * @var \App\Payment\Contracts\Payment
     */
    private $payment;

    /**
     * @var string
     */
    private $checkoutUrl;

    private function __construct(Payment $payment, string $checkoutUrl)
    {
        $this->payment = $payment;
        $this->checkoutUrl = $checkoutUrl;
    }

    public static function make(Payment $payment, string $checkoutUrl)
    {
        return new static($payment, $checkoutUrl);
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function getCheckoutUrl(): string
    {
        return $this->checkoutUrl;
    }
}
