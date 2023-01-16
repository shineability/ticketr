<?php

namespace Tests\Fake;

use App\Payment\Contracts\Payment;

final class FakePayment implements Payment
{
    private string $status;

    private string $transactionId;

    private function __construct(string $status, ?string $transactionId = null)
    {
        $this->status = $status;
        $this->transactionId = $transactionId ?? fake()->uuid();
    }

    public static function withStatus(string $status, ?string $transactionId = null): self
    {
        return new self($status, $transactionId);
    }

    public static function completed(?string $transactionId = null): self
    {
        return self::withStatus('completed', $transactionId);
    }

    public static function canceled(?string $transactionId = null): self
    {
        return self::withStatus('canceled', $transactionId);
    }

    public function isCompleted(): bool
    {
        return $this->status() === 'completed';
    }

    public function isCanceled(): bool
    {
        return $this->status() === 'canceled';
    }

    public function status(): string
    {
        return $this->status;
    }

    public function transactionId(): string
    {
        return $this->transactionId;
    }
}
