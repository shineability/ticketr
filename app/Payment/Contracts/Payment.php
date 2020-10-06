<?php

namespace App\Payment\Contracts;

interface Payment
{
    public function isCompleted(): bool;
    public function isCanceled(): bool;
    public function getStatus(): string;
    public function getOrderId(): string;
    public function getTransactionId(): string;
}
