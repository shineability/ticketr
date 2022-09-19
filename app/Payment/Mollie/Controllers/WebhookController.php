<?php

namespace App\Payment\Mollie\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Payment\PaymentProviderFactory;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    private PaymentProviderFactory $providerFactory;

    public function __construct(PaymentProviderFactory $providerFactory)
    {
        $this->providerFactory = $providerFactory;
    }

    public function __invoke(Request $request)
    {
        $transactionId = $request->id;
        $order = Order::findByTransactionId($transactionId);
        $provider = $this->providerFactory->createForOrder($order);
        $payment = $provider->getPayment($transactionId);

        $order->processPayment($payment);
    }
}
