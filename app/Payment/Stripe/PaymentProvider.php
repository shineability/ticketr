<?php

namespace App\Payment\Stripe;

use App\Order;
use App\Payment\Contracts\PaymentProvider as PaymentProviderContract;
use App\Payment\Contracts\Payment as PaymentContract;
use Stripe\StripeClient;
use App\Payment\Stripe\Payment as StripePayment;
use Stripe\Checkout\Session as StripeCheckoutSession;
use App\Payment\PaymentResponse;
use Illuminate\Http\Request;

final class PaymentProvider implements PaymentProviderContract
{
    /**
     * @var \Stripe\StripeClient
     */
    private $client;

    public function __construct(StripeClient $client)
    {
        $this->client = $client;
    }

    public function name(): string
    {
        return 'Stripe Checkout';
    }

    public function getPayment(string $transactionId): PaymentContract
    {
        return new StripePayment($this->client->checkout->sessions->retrieve($transactionId));
    }

    public function checkout(Order $order): PaymentResponse
    {
        $session = $this->createSession($order);

        $checkoutUrl = route('stripe.checkout.redirect', [
            'organizer' => $order->organizer,
            'session_id' => $session->id
        ]);

        return PaymentResponse::make(new StripePayment($session), $checkoutUrl);
    }

    private function createSession(Order $order): StripeCheckoutSession
    {
        return $this->client->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'customer_email' => $order->email,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => strtolower($order->total->getCurrency()),
                        'product_data' => [
                            'name' => $order->ticket->title,
                        ],
                        'unit_amount' => $order->total->getAmount(),
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('checkout.redirect.order', ['order' => $order]),
            'cancel_url' => route('stripe.checkout.cancel', ['order' => $order])
        ]);
    }
}
