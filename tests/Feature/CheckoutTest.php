<?php

namespace Tests\Feature;

use App\Models\Organizer;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

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
        $this->markTestSkipped('Implement payment provider fake');

        $this->withoutExceptionHandling();

        $ticket = Ticket::factory()->forOrganizer(['payment_provider' => 'free'])->create();

        $response = $this->post(route('checkout.form', $ticket), ['email' => 'valid@email.dev']);

        $response
            ->assertStatus(302);
    }
}
