<?php

return [

    'stripe' => [
        'secret_key' => env('STRIPE_SECRET_KEY')
    ],

    'mollie' => [
        'api_key' => env('MOLLIE_API_KEY')
    ]

];
