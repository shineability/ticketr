<?php

namespace App\Payment\Mollie;

use App\Models\Order;
use App\Payment\Contracts\Payment as PaymentContract;
use App\Payment\Contracts\PaymentProvider as PaymentProviderContract;
use App\Payment\Mollie\Payment as MolliePayment;
use App\Payment\PaymentResponse;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment as MollieApiResourcePayment;

final class PaymentProvider implements PaymentProviderContract
{
    /**
     * Create a new Mollie payment provider.
     */
    public function __construct(
        private MollieApiClient $client,
        private string $webhookUrl
    ) {}

    /**
     * Get the payment provider name.
     */
    public function name(): string
    {
        return 'Mollie';
    }

    /**
     * Create a checkout for an order.
     */
    public function checkout(Order $order): PaymentResponse
    {
        $payment = $this->createPayment($order);
        $checkoutUrl = $payment->getCheckoutUrl();

        return PaymentResponse::make(new MolliePayment($payment), $checkoutUrl);
    }

    /**
     * Get a payment by transaction ID.
     */
    public function getPayment(string $transactionId): PaymentContract
    {
        return new MolliePayment($this->client->payments->get($transactionId));
    }

    /**
     * Create a Mollie payment for an order.
     */
    private function createPayment(Order $order): MollieApiResourcePayment
    {
        return $this->client->payments->create([
            'amount' => [
                'currency' => $order->total->getCurrency(),
                'value' => number_format($order->total->getAmount() / 100, 2, '.', ''),
            ],
            'description' => sprintf('%s order #%s', config('app.name'), $order->reference),
            'redirectUrl' => route('checkout.redirect.order', ['order' => $order]),
            'webhookUrl' => $this->webhookUrl,
            'metadata' => [
                'order_id' => $order->uuid,
            ],
        ]);
    }
}
