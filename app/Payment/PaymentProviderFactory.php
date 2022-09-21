<?php

namespace App\Payment;

use App\Models\Organizer;
use App\Models\Order;
use App\Payment\Contracts\PaymentProvider;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Closure;

final class PaymentProviderFactory
{
    private Container $container;
    private array $providers = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Create payment provider based on type.
     *
     * @param  string $provider
     * @return PaymentProvider
     * @throws InvalidArgumentException
     */
    public function create(string $provider): PaymentProvider
    {
        if (isset($this->providers[$provider])) {
            return $this->providers[$provider]($this->container);
        }

        throw new InvalidArgumentException("Payment provider [$provider] not supported");
    }

    /**
     * @param  Organizer $organizer
     * @return PaymentProvider
     */
    public function createForOrganizer(Organizer $organizer): PaymentProvider
    {
        return $this->create($organizer->payment_provider);
    }

    /**
     * @param  Order $order
     * @return PaymentProvider
     */
    public function createForOrder(Order $order): PaymentProvider
    {
        return $this->createForOrganizer($order->ticket->organizer);
    }

    /**
     * Register a custom payment provider.
     *
     * @param  string  $provider
     * @param  Closure  $callback
     * @return $this
     */
    public function extend($provider, Closure $callback): self
    {
        $this->providers[$provider] = $callback;

        return $this;
    }
}
