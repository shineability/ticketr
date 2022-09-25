<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Organizer;
use App\Models\Ticket;
use App\Payment\PaymentProviderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Session;
use Tests\Fake\FakePayment;
use Tests\Fake\FakePaymentProvider;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_shows_all_the_available_tickets()
    {
        Ticket::factory()
            ->count(3)
            ->sequence(fn($sequence) => ['title' => ['Abba', 'The Beatles', 'Moby'][$sequence->index]])
            ->for(Organizer::factory()->create(['name' => 'Trix']))
            ->create();

        Ticket::factory()
            ->count(2)
            ->sequence(fn($sequence) => ['title' => ['Metallica', 'INXS'][$sequence->index]])
            ->for(Organizer::factory()->create(['name' => 'Sportpaleis']))
            ->create();

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee(config('app.name'))
            ->assertSee(['Trix', 'Sportpaleis'])
            ->assertSeeInOrder(['Abba', 'The Beatles', 'Moby', 'Metallica', 'INXS']);
    }

    public function test_it_displays_the_checkout_form()
    {
        $ticket = Ticket::factory()->create();

        $response = $this->get(route('checkout.form', $ticket));

        $response
            ->assertOk()
            ->assertSee(config('app.name'))
            ->assertSee($ticket->title)
            ->assertSee($ticket->formattedPrice)
            ->assertSee($ticket->organizer->name);
    }

    public function test_it_requires_an_email_address_to_order_a_ticket()
    {
        $ticket = Ticket::factory()->create();

        $response = $this->post(route('checkout.form', $ticket));

        $response
            ->assertStatus(302)
            ->assertInvalid(['email' => 'Please fill in a valid email address']);
    }

    public function test_it_requires_a_valid_email_address_to_order_a_ticket()
    {
        $ticket = Ticket::factory()->create();

        $response = $this->post(route('checkout.form', $ticket), ['email' => 'invalid_email_address']);

        $response
            ->assertStatus(302)
            ->assertInvalid(['email' => 'Please fill in a valid email address']);
    }

    public function test_it_redirects_to_the_payment_provider()
    {
        $ticket = Ticket::factory()
            ->forOrganizer(['payment_provider' => 'fake'])
            ->create();

        $payment = FakePayment::withStatus('open');
        $checkoutUrl = $this->faker->url();
        $email = $this->faker->safeEmail();

        app(PaymentProviderFactory::class)->extend('fake',
            function () use ($checkoutUrl, $payment) {
                return FakePaymentProvider::withPayment($payment, $checkoutUrl);
            }
        );

        $response = $this->post(route('checkout.form', $ticket), ['email' => $email]);

        $response->assertRedirect($checkoutUrl);

        $this->assertDatabaseHas('orders', [
            'ticket_id' => $ticket->id,
            'email' => $email,
            'status' => 'pending',
            'payment_status' => $payment->status(),
            'payment_transaction_id' => $payment->transactionId()
        ]);
    }

    public function test_it_redirects_to_the_homepage_when_payment_is_processed()
    {
        $order = Order::factory()->completed()->create();

        $response = $this->get(route('checkout.redirect.order', $order));

        $response
            ->assertRedirect(route('home'))
            ->assertSessionHas('checkout.order', fn($value) => $order->is($value));
    }

    public function test_it_displays_a_message_when_completed()
    {
        $order = Order::factory()->completed()->create();

        Session::flash('checkout.order', $order);

        $response = $this->get(route('home'));

        $response
            ->assertSeeText('Thank you for your order!')
            ->assertSeeText($order->email);
    }

    public function test_it_displays_a_message_when_canceled()
    {
        $order = Order::factory()->canceled()->create();

        Session::flash('checkout.order', $order);

        $response = $this->get(route('home'));

        $response->assertSeeText('Your order has been canceled...');
    }
}
