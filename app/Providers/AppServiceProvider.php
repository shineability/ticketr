<?php

namespace App\Providers;

use App\Faker\TicketrProvider;
use App\Payment\Free\PaymentProvider as FreePaymentProvider;
use App\Payment\Mollie\PaymentProvider as MolliePaymentProvider;
use App\Payment\PaymentProviderFacade as PaymentProvider;
use App\Payment\PaymentProviderFactory;
use App\Payment\Stripe\PaymentProvider as StripePaymentProvider;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Mollie\Api\MollieApiClient;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use NumberFormatter;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerPaymentProviderFactory();
        $this->registerMoneyFormatter();
        $this->registerTicketrProviderForFaker();
    }

    /**
     * Register the payment provider factory.
     */
    private function registerPaymentProviderFactory(): void
    {
        $this->app->singleton(PaymentProviderFactory::class, function (Container $app) {
            return new PaymentProviderFactory($app);
        });

        $this->app->alias(PaymentProviderFactory::class, 'payment.provider.factory');

        AliasLoader::getInstance()->alias('PaymentProvider', PaymentProvider::class);
    }

    /**
     * Register the money formatter.
     */
    private function registerMoneyFormatter(): void
    {
        $this->app->singleton('money.formatter', function () {
            $currencies = new ISOCurrencies;
            $numberFormatter = new NumberFormatter('nl_BE', NumberFormatter::CURRENCY);

            return new IntlMoneyFormatter($numberFormatter, $currencies);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPaymentProviders();
    }

    /**
     * Register all payment providers.
     */
    private function registerPaymentProviders(): void
    {
        // Mollie payment provider
        PaymentProvider::extend('mollie', function () {
            $client = new MollieApiClient;
            $client->setApiKey(config('payment.mollie.api_key'));

            return new MolliePaymentProvider($client, $this->generateSharedWebhookUrlForMollie());
        });

        // Stripe payment provider
        PaymentProvider::extend('stripe', function () {
            $client = new StripeClient(config('payment.stripe.secret_key'));

            return new StripePaymentProvider($client);
        });

        // Free payment provider (for local/testing only)
        if (app()->environment('local', 'testing')) {
            PaymentProvider::extend('free', function () {
                return new FreePaymentProvider;
            });
        }
    }

    /**
     * Generate the shared webhook URL for Mollie.
     */
    private function generateSharedWebhookUrlForMollie(): string
    {
        $url = route('mollie.webhook');

        if (app()->isLocal()) {
            return str_replace(config('app.url'), config('app.share_url'), $url);
        }

        return $url;
    }

    /**
     * Register the Ticketr Faker provider.
     */
    private function registerTicketrProviderForFaker(): void
    {
        $this->app->singleton(Generator::class, function () {
            $faker = Factory::create();
            $faker->addProvider(new TicketrProvider($faker));

            return $faker;
        });
    }
}
