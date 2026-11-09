<?php

namespace App\Payment\Stripe\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Payment\PaymentProviderFactory;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private PaymentProviderFactory $providerFactory
    ) {}

    /**
     * Handle the Stripe webhook.
     */
    public function __invoke(Request $request): void
    {
        $transactionId = $request->json('data.object.id');
        $order = Order::findByTransactionId($transactionId);

        if ($order === null) {
            return;
        }

        $provider = $this->providerFactory->createForOrder($order);
        $payment = $provider->getPayment($transactionId);

        $order->processPayment($payment);
    }
}
