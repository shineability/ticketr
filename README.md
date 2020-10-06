# Ticketr


## Installation

1. Clone this repository with `git clone git@github.com:Pointbreak-co/technical-assessment-1-backend-developer-shineability.git`
2. Run `composer install` to install the dependencies
3. Run `./vendor/bin/homestead make` and `vagrant up` 
4. Drink ☕
5. Run `vagrant ssh` and cd into `/home/vagrant/code`
6. Run `composer setup` to run the migrations
7. Setup a working email driver like __[Mailtrap](https://mailtrap.io/)__

To be able to receive webhook requests, when running the project locally, from (some of) the payment providers, Ngrok will be used. Run `share ticketr.test` in the Vagrant box. After running the command, you will see an Ngrok screen appear containing the activity log and the publicly accessible URL, which needs to be copied to your `.env` file: 

```
NGROK_URL=https://7f1ff31072b3.ngrok.io
```

## Payment providers

Currently three payment providers are available for Ticketr: **Mollie**, **Stripe** and
**Free**.

### Implementation

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