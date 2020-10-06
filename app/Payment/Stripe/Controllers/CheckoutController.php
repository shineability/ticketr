<?php

namespace App\Payment\Stripe\Controllers;

use App\Order;
use App\Organizer;
use Illuminate\Http\Request;

class CheckoutController
{
    public function redirect(Request $request, Organizer $organizer)
    {
        return view('stripe.checkout.redirect')->with([
            'stripe_publishable_key' => $organizer->payment_provider_config['publishable_key'],
            'stripe_session_id' => $request->session_id
        ]);
    }

    public function cancel(Order $order)
    {
        $order->cancel();

        return redirect()->route('checkout.redirect.order', ['order' => $order]);
    }
}
