<?php

namespace App\Payment\Free;

use App\Models\Order;
use App\Payment\Contracts\Payment as PaymentContract;
use App\Payment\Contracts\PaymentProvider as PaymentProviderContract;
use App\Payment\PaymentResponse;

final class PaymentProvider implements PaymentProviderContract
{
    /**
     * Get the payment provider name.
     */
    public function name(): string
    {
        return 'Free';
    }

    /**
     * Create a checkout for an order.
     */
    public function checkout(Order $order): PaymentResponse
    {
        $payment = Payment::completed($order);
        $checkoutUrl = route('checkout.redirect.order', ['order' => $order]);

        return PaymentResponse::make($payment, $checkoutUrl);
    }

    /**
     * Get a payment by transaction ID.
     */
    public function getPayment(string $transactionId): PaymentContract
    {
        $order = Order::findByTransactionId($transactionId);

        return Payment::completed($order);
    }
}
