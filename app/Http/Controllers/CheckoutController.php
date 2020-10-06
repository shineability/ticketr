<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\Order;
use App\Payment\PaymentProviderFactory;
use App\Http\Requests\CheckoutRequest;

class CheckoutController extends Controller
{
    /**
     * @var PaymentProviderFactory
     */
    private $providerFactory;

    public function __construct(PaymentProviderFactory $providerFactory)
    {
        $this->providerFactory = $providerFactory;
    }

    /**
     * Show available tickets.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('checkout.tickets', ['tickets' => Ticket::with('organizer')->get()]);
    }

    /**
     * Show ticket form.
     *
     * @param  Ticket $ticket
     * @return \Illuminate\View\View
     */
    public function showTicketForm(Ticket $ticket)
    {
        return view('checkout.form', ['ticket' => $ticket]);
    }

    /**
     * Redirect to payment provider checkout.
     *
     * @param  CheckoutRequest $request
     * @param  Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToPaymentProvider(CheckoutRequest $request, Ticket $ticket)
    {
        $order = Order::pending($ticket, $request->email);
        $provider = $this->providerFactory->createForOrganizer($ticket->organizer);
        $response = $provider->checkout($order);
        $order->processPayment($response->getPayment());

        return redirect($response->getCheckoutUrl());
    }

    /**
     * Redirect to homepage and show order status message.
     *
     * @param  Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectOrder(Order $order)
    {
        return redirect()->route('home')->with('checkout.order', $order);
    }
}
