# Ticketr

The instructions for the assessment can be found [**here**](ASSESSMENT.md).

## Requirements

- PHP 8.1 or later
- Docker

## Installation

1. Clone this repository with `git clone git@github.com:shineability/ticketr.git`
2. Run `composer install` to install the dependencies
3. Run `./vendor/bin/sail up` 
4. Wait for it ☕
5. Run `./vendor/bin/sail composer setup` to run the migrations
6. Visit `http://localhost` to run the app and view sent emails using Mailhog at `http://localhost:8025`

## Todo

- Write tests
- Move payment provider credentials from database to configuration (`.env`)


## Webhooks

To be able to receive webhook requests when running the project locally, [**Expose**](https://expose.dev/docs/introduction) will be used to share the local URL. Run `./vendor/bin/sail share` and copy the public URL ([**use the HTTP one**](https://github.com/laravel/sail/issues/216)) to your `.env` file, e.g.: 

```
SHARE_URL=http://foobar.laravel-sail.site:8080
```
This will make webhooks available for the **Mollie implementation**, since the webhook URL can be sent along as a parameter when [**creating a payment using the Mollie API**](https://docs.mollie.com/reference/v2/payments-api/create-payment).  

For the **Stripe implementation** the webhook URL needs to be configured in the Stripe backend, so [**send me**](https://github.com/shineability) the share URL if you want to see it in action ;)

## Payment providers

Currently three payment providers are available for Ticketr: **Mollie**, **Stripe** and
**Free**.

### Implementation

This application implements the [**Strategy pattern**](https://refactoring.guru/design-patterns/strategy) to be able to switch between different payment provider implementations at runtime.

Each payment provider should implement the `App\Payment\Contracts\PaymentProvider` interface:

```
interface PaymentProvider
{
    public function name(): string;
    public function checkout(Order $order): PaymentResponse;
    public function getPayment(string $transactionId): Payment;
}
```
The `PaymentProvider::checkout()` method returns a `PaymentResponse` which has a `Payment` and a checkout URL. 

The `Payment` will be used to update its initial state to the associated `Order`. 

After that, the customer will be redirected to the provider's checkout page URL, as defined by `PaymentResponse::getCheckoutUrl()`.

The `PaymentProvider::getPayment()` is used in the respective webhook controllers to fetch the payment and its updates from the provider by using the  `$transactionId` which was initially saved on the `Order`. 


### Adding custom providers

Custom providers can be added using `App\Payment\PaymentProviderFactory`. The factory can be easily accessed by its facade `PaymentProvider`.

```
PaymentProvider::extend('stripe', function ($config, $app) {
	$client = new \Stripe\StripeClient($config['secret_key']);
	return new \App\Payment\Stripe\PaymentProvider($client);
});
```

## License

The MIT License. Please see [the license file](LICENSE.md) for more information.
