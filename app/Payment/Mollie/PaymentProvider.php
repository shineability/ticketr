<?php

namespace App\Payment\Mollie;

use App\Order;
use App\Payment\Contracts\PaymentProvider as PaymentProviderContract;
use App\Payment\Contracts\Payment as PaymentContract;
use Mollie\Api\MollieApiClient;
use App\Payment\Mollie\Payment as MolliePayment;
use Mollie\Api\Resources\Payment as MollieApiResourcePayment;
use App\Payment\PaymentResponse;

final class PaymentProvider implements PaymentProviderContract
{
    /**
     * @var \Mollie\Api\MollieApiClient
     */
    private $client;

    public function __construct(MollieApiClient $client)
    {
        $this->client = $client;
    }

    public function name(): string
    {
        return 'Mollie';
    }

    public function checkout(Order $order): PaymentResponse
    {
        $payment = $this->createPayment($order);
        $checkoutUrl = $payment->getCheckoutUrl();

        return PaymentResponse::make(new MolliePayment($payment), $checkoutUrl);
    }

    public function getPayment(string $transactionId): PaymentContract
    {
        return new MolliePayment($this->client->payments->get($transactionId));
    }

    private function createPayment(Order $order): MollieApiResourcePayment
    {
        return $this->client->payments->create([
            'amount' => [
                'currency' => $order->total->getCurrency(),
                'value' => number_format($order->total->getAmount() / 100, 2, '.', '')
            ],
            'description' => $order->payment_description,
            'redirectUrl' => route('checkout.redirect.order', ['order' => $order->uuid]),
            'webhookUrl'  => $this->getWebhookUrl(),
            'metadata' => [
                'order_id' => $order->uuid,
            ],
        ]);
    }

    private function getWebhookUrl(): string
    {
        $url = route('mollie.webhook');

        if (app()->isLocal()) {
            return str_replace(config('app.url'), config('app.ngrok_url'), $url);
        }

        return $url;
    }
}