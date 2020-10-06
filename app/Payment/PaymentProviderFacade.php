<?php

namespace App\Payment;

use Illuminate\Support\Facades\Facade;

class PaymentProviderFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'payment.provider.factory';
    }
}
