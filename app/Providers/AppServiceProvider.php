<?php

namespace App\Providers;

use App\Faker\TicketrProvider;
use Faker\{Factory, Generator};
use Illuminate\{Support\ServiceProvider, Foundation\AliasLoader};
use App\Payment\PaymentProviderFactory;
use App\Payment\PaymentProviderFacade as PaymentProvider;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use NumberFormatter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerPaymentProviderFactory();
        $this->registerMoneyFormatter();
        $this->registerTicketrProviderForFaker();
    }

    public function registerPaymentProviderFactory(): void
    {
        $this->app->singleton(PaymentProviderFactory::class, function ($app) {
            return new PaymentProviderFactory($app);
        });

        $this->app->alias(PaymentProviderFactory::class, 'payment.provider.factory');

        AliasLoader::getInstance()->alias('PaymentProvider', \App\Payment\PaymentProviderFacade::class);
    }

    public function registerMoneyFormatter(): void
    {
        $this->app->singleton('money.formatter', function () {
            $currencies = new ISOCurrencies();
            $numberFormatter = new NumberFormatter('nl_BE', NumberFormatter::CURRENCY);
            return new IntlMoneyFormatter($numberFormatter, $currencies);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        PaymentProvider::extend('mollie', function () {
            $client = new \Mollie\Api\MollieApiClient();
            $client->setApiKey(config('payment.mollie.api_key'));
            return new \App\Payment\Mollie\PaymentProvider($client, $this->generateSharedWebhookUrlForMollie());
        });

        PaymentProvider::extend('stripe', function () {
            $client = new \Stripe\StripeClient(config('payment.stripe.secret_key'));
            return new \App\Payment\Stripe\PaymentProvider($client);
        });

        PaymentProvider::extend('free', function () {
            return new \App\Payment\Free\PaymentProvider();
        });
    }

    private function generateSharedWebhookUrlForMollie(): string
    {
        $url = route('mollie.webhook');

        if (app()->isLocal()) {
            return str_replace(config('app.url'), config('app.share_url'), $url);
        }

        return $url;
    }

    private function registerTicketrProviderForFaker(): void
    {
        $this->app->singleton(Generator::class, function () {
            $faker = Factory::create();
            $faker->addProvider(new TicketrProvider($faker));
            return $faker;
        });
    }
}
