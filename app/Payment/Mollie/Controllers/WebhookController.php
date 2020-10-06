<?php

namespace App\Payment\Mollie\Controllers;

use App\Http\Controllers\Controller;
use App\Payment\PaymentProviderFactory;
use Illuminate\Http\Request;
use App\Order;

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

    public function __invoke(Request $request)
    {
        $transactionId = $request->id;
        $order = Order::findByTransactionId($request->id);
        $provider = $this->providerFactory->createForOrder($order);
        $payment = $provider->getPayment($transactionId);

        $order->processPayment($payment);
    }
}
