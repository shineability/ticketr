<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\Ticket;
use App\Payment\PaymentProviderFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private PaymentProviderFactory $providerFactory
    ) {}

    /**
     * Show available tickets.
     */
    public function index(): View
    {
        return view('checkout.tickets', ['tickets' => Ticket::all()]);
    }

    /**
     * Show ticket form.
     */
    public function showTicketForm(Ticket $ticket): View
    {
        return view('checkout.form', ['ticket' => $ticket]);
    }

    /**
     * Redirect to payment provider checkout.
     */
    public function redirectToPaymentProvider(CheckoutRequest $request, Ticket $ticket): RedirectResponse
    {
        $order = Order::pending($ticket, $request->email, $request->quantity);
        $provider = $this->providerFactory->createForOrganizer($ticket->organizer);
        $response = $provider->checkout($order);
        $order->processPayment($response->payment());

        return redirect($response->checkoutUrl());
    }

    /**
     * Redirect to homepage and show order status message.
     */
    public function redirectOrder(Order $order): RedirectResponse
    {
        return redirect()
            ->route('home')
            ->with('checkout.order', $order);
    }
}
