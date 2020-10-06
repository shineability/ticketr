<?php

namespace App\Payment;

use App\Organizer;
use App\Order;
use App\Payment\Contracts\PaymentProvider;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Closure;

final class PaymentProviderFactory
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    private $container;

    /**
     * @var array
     */
    private $providers = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Create payment provider based on type and configuration settings.
     *
     * @param  string $provider
     * @param  Config $config
     * @return \App\Payment\Contracts\PaymentProvider
     */
    public function create(string $provider, Config $config): PaymentProvider
    {
        if (isset($this->providers[$provider])) {
            return $this->providers[$provider]($config, $this->container);
        }

        throw new InvalidArgumentException("Payment provider [$provider] not supported");
    }

    /**
     * @param  Organizer $organizer
     * @return \App\Payment\Contracts\PaymentProvider
     */
    public function createForOrganizer(Organizer $organizer): PaymentProvider
    {
        $provider = $organizer->payment_provider;
        $config = new Config($organizer->payment_provider_config);

        return $this->create($provider, $config);
    }

    /**
     * @param  Order $order
     * @return \App\Payment\Contracts\PaymentProvider
     */
    public function createForOrder(Order $order): PaymentProvider
    {
        return $this->createForOrganizer($order->organizer);
    }

    /**
     * Register a custom payment provider.
     *
     * @param  string  $provider
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($provider, Closure $callback)
    {
        $this->providers[$provider] = $callback;

        return $this;
    }
}
