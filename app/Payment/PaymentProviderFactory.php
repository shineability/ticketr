<?php

namespace App\Payment;

use App\Models\Order;
use App\Models\Organizer;
use App\Payment\Contracts\PaymentProvider;
use Closure;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

final class PaymentProviderFactory
{
    /**
     * The registered payment providers.
     */
    private array $providers = [];

    /**
     * Create a new payment provider factory.
     */
    public function __construct(
        private Container $container
    ) {}

    /**
     * Create payment provider based on type.
     *
     * @throws InvalidArgumentException If the payment provider is not registered
     */
    public function create(string $provider): PaymentProvider
    {
        if (isset($this->providers[$provider])) {
            return $this->providers[$provider]($this->container);
        }

        throw new InvalidArgumentException("Payment provider [$provider] not supported");
    }

    /**
     * Create a payment provider for an organizer.
     */
    public function createForOrganizer(Organizer $organizer): PaymentProvider
    {
        return $this->create($organizer->payment_provider);
    }

    /**
     * Create a payment provider for an order.
     */
    public function createForOrder(Order $order): PaymentProvider
    {
        return $this->createForOrganizer($order->ticket->organizer);
    }

    /**
     * Register a custom payment provider.
     */
    public function extend(string $provider, Closure $callback): self
    {
        $this->providers[$provider] = $callback;

        return $this;
    }
}
