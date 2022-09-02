<?php

namespace App\Providers;

use Illuminate\{Support\ServiceProvider, Foundation\AliasLoader};
use App\Payment\PaymentProviderFactory;
use App\Payment\PaymentProviderFacade as PaymentProvider;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use NumberFormatter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPaymentProviderFactory();
        $this->registerMoneyFormatter();
    }

    public function registerPaymentProviderFactory()
    {
        $this->app->singleton(PaymentProviderFactory::class, function ($app) {
            return new PaymentProviderFactory($app);
        });

        $this->app->alias(PaymentProviderFactory::class, 'payment.provider.factory');

        AliasLoader::getInstance()->alias('PaymentProvider', \App\Payment\PaymentProviderFacade::class);
    }

    public function registerMoneyFormatter()
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
    public function boot()
    {
        PaymentProvider::extend('mollie', function ($config) {
            $client = new \Mollie\Api\MollieApiClient();
            $client->setApiKey($config['api_key']);
            return new \App\Payment\Mollie\PaymentProvider($client, $this->generateSharedWebhookUrlForMollie());
        });

        PaymentProvider::extend('stripe', function ($config) {
            $client = new \Stripe\StripeClient($config['secret_key']);
            return new \App\Payment\Stripe\PaymentProvider($client);
        });

        PaymentProvider::extend('free', function ($config) {
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
}
