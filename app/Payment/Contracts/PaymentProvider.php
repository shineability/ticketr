<?php

namespace App\Payment\Contracts;

use App\Order;
use App\Payment\Contracts\Payment;
use App\Payment\PaymentResponse;

interface PaymentProvider
{
    public function name(): string;
    public function getPayment(string $transactionId): Payment;
    public function checkout(Order $order): PaymentResponse;
}
