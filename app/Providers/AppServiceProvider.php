<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use App\Payment\PaymentProviderFactory;
use PaymentProvider;
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
            return new \App\Payment\Mollie\PaymentProvider($client);
        });

        PaymentProvider::extend('stripe', function ($config) {
            $client = new \Stripe\StripeClient($config['secret_key']);
            return new \App\Payment\Stripe\PaymentProvider($client);
        });

        PaymentProvider::extend('free', function ($config) {
            return new \App\Payment\Free\PaymentProvider();
        });
    }
}
