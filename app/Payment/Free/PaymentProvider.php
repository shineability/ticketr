<?php

namespace App\Payment\Free;

use App\Models\Order;
use App\Payment\Contracts\PaymentProvider as PaymentProviderContract;
use App\Payment\Contracts\Payment as PaymentContract;
use App\Payment\PaymentResponse;

final class PaymentProvider implements PaymentProviderContract
{
    public function name(): string
    {
        return 'Free';
    }

    public function checkout(Order $order): PaymentResponse
    {
        $payment = Payment::completed($order);
        $checkoutUrl = route('checkout.redirect.order', ['order' => $order]);

        return PaymentResponse::make($payment, $checkoutUrl);
    }

    public function getPayment(string $transactionId): PaymentContract
    {
        return Payment::completed(Order::findByTransactionId($transactionId));
    }
}
