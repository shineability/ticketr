<?php

namespace Tests\Fake;

use App\Models\Order;
use App\Payment\Contracts\Payment;
use App\Payment\Contracts\PaymentProvider;
use App\Payment\PaymentResponse;

final class FakePaymentProvider implements PaymentProvider
{
    private Payment $payment;
    private string $checkoutUrl;

    private function __construct(Payment $payment, string $checkoutUrl)
    {
        $this->payment = $payment;
        $this->checkoutUrl = $checkoutUrl;
    }

    public static function withPayment(Payment $payment, string $checkoutUrl)
    {
        return new self($payment, $checkoutUrl);
    }

    public function name(): string
    {
        return 'Fake';
    }

    public function checkout(Order $order): PaymentResponse
    {
        return PaymentResponse::make($this->payment, $this->checkoutUrl);
    }

    public function getPayment(string $transactionId): Payment
    {
        return $this->payment;
    }
}
