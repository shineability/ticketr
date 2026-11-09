<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe Payment Gateway
    |--------------------------------------------------------------------------
    */
    'stripe' => [
        'secret_key' => env('STRIPE_SECRET_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mollie Payment Gateway
    |--------------------------------------------------------------------------
    */
    'mollie' => [
        'api_key' => env('MOLLIE_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    |
    | This is the default payment provider that will be used if no specific
    | provider is configured for an organizer.
    |
    */
    'default' => env('PAYMENT_PROVIDER_DEFAULT', 'free'),

];
