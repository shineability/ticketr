<?php

namespace App\Payment\Stripe;

use App\Models\Order;
use App\Payment\Contracts\Payment as PaymentContract;
use App\Payment\Contracts\PaymentProvider as PaymentProviderContract;
use App\Payment\PaymentResponse;
use App\Payment\Stripe\Payment as StripePayment;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\StripeClient;

final class PaymentProvider implements PaymentProviderContract
{
    /**
     * Create a new Stripe payment provider.
     */
    public function __construct(
        private StripeClient $client
    ) {}

    /**
     * Get the payment provider name.
     */
    public function name(): string
    {
        return 'Stripe Checkout';
    }

    /**
     * Get a payment by transaction ID.
     */
    public function getPayment(string $transactionId): PaymentContract
    {
        return new StripePayment($this->client->checkout->sessions->retrieve($transactionId));
    }

    /**
     * Create a checkout for an order.
     */
    public function checkout(Order $order): PaymentResponse
    {
        $session = $this->createSession($order);

        return PaymentResponse::make(new StripePayment($session), $session->url);
    }

    /**
     * Create a Stripe checkout session for an order.
     */
    private function createSession(Order $order): StripeCheckoutSession
    {
        return $this->client->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'customer_email' => $order->email,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => strtolower($order->ticket->price->getCurrency()),
                        'product_data' => [
                            'name' => $order->ticket->title,
                        ],
                        'unit_amount' => $order->ticket->price->getAmount(),
                    ],
                    'quantity' => $order->quantity,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('checkout.redirect.order', ['order' => $order]),
            'cancel_url' => route('home'),
        ]);
    }
}
