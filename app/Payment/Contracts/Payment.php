<?php

namespace App\Payment\Contracts;

interface Payment
{
    public function isCompleted(): bool;
    public function isCanceled(): bool;
    public function status(): string;
    public function orderId(): string;
    public function transactionId(): string;
}
