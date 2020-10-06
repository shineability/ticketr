<?php

namespace App\Payment\Stripe\Controllers;

use App\Http\Controllers\Controller;
use App\Payment\PaymentProviderFactory;
use Illuminate\Http\Request;
use App\Order;
use Illuminate\Log\Logger;

class WebhookController extends Controller
{
    /**
     * @var PaymentProviderFactory
     */
    private $providerFactory;

    public function __construct(PaymentProviderFactory $providerFactory)
    {
        $this->providerFactory = $providerFactory;
    }

    public function __invoke(Request $request, Logger $logger)
    {
        $transactionId = $request->json('data.object.id');
        $order = Order::findByTransactionId($transactionId);
        $provider = $this->providerFactory->createForOrder($order);
        $payment = $provider->getPayment($transactionId);

        $order->processPayment($payment);
    }
}
