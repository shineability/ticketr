<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Order;
use App\Payment\PaymentProviderFactory;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    private PaymentProviderFactory $providerFactory;

    public function __construct(PaymentProviderFactory $providerFactory)
    {
        $this->providerFactory = $providerFactory;
    }

    /**
     * Show available tickets.
     *
     * @return View
     */
    public function index(): View
    {
        return view('checkout.tickets', ['tickets' => Ticket::all()]);
    }

    /**
     * Show ticket form.
     *
     * @param  Ticket $ticket
     * @return View
     */
    public function showTicketForm(Ticket $ticket): View
    {
        return view('checkout.form', ['ticket' => $ticket]);
    }

    /**
     * Redirect to payment provider checkout.
     *
     * @param  CheckoutRequest $request
     * @param  Ticket $ticket
     * @return RedirectResponse
     */
    public function redirectToPaymentProvider(CheckoutRequest $request, Ticket $ticket): RedirectResponse
    {
        $order = Order::pending($ticket, $request->email);
        $provider = $this->providerFactory->createForOrganizer($ticket->organizer);
        $response = $provider->checkout($order);
        $order->processPayment($response->payment());

        return redirect($response->checkoutUrl());
    }

    /**
     * Redirect to homepage and show order status message.
     *
     * @param  Order $order
     * @return RedirectResponse
     */
    public function redirectOrder(Order $order): RedirectResponse
    {
        return redirect()
            ->route('home')
            ->with('checkout.order', $order);
    }
}
