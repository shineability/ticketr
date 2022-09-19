<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Ticket;
use App\Events\OrderCompleted;
use App\Mail\OrderCompleted as OrderCompletedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Money\Money;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_can_create_a_pending_order()
    {
        $ticket = Ticket::factory()->create();
        $email = $this->faker->safeEmail();

        $order = Order::pending($ticket, $email);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'ticket_id' => $ticket->id,
            'email' => $email,
            'status' => 'pending',
        ]);
    }

    public function test_it_can_be_completed()
    {
        Event::fake(OrderCompleted::class);

        $ticket = Ticket::factory()->create();
        $email = $this->faker->safeEmail();

        $order = Order::pending($ticket, $email);

        $order->complete();

        $this->assertDatabaseHas($order, [
            'id' => $order->id,
            'status' => 'completed'
        ]);

        Event::assertDispatched(OrderCompleted::class);
    }

    public function test_it_sends_a_confirmation_email_when_completed()
    {
        Mail::fake();

        $ticket = Ticket::factory()->create();
        $email = $this->faker->safeEmail();

        $order = Order::pending($ticket, $email);

        $order->complete();

        Mail::assertQueued(OrderCompletedMail::class);
    }

    public function test_it_can_be_canceled()
    {
        $ticket = Ticket::factory()->create();
        $email = $this->faker->safeEmail();

        $order = Order::pending($ticket, $email);

        $order->cancel();

        $this->assertDatabaseHas($order, [
            'id' => $order->id,
            'status' => 'canceled'
        ]);
    }

    public function test_it_can_be_found_by_transaction_id()
    {
        $transactionId = $this->faker->uuid();
        Order::factory()->create(['payment_transaction_id' => $transactionId]);

        $order = Order::findByTransactionId($transactionId);

        $this->assertEquals($transactionId, $order->payment_transaction_id);
    }

    public function test_it_generates_a_reference()
    {
        $order = Order::factory()->create();

        $this->assertEquals('00000001', $order->reference);
    }

    public function test_it_calculates_a_total()
    {
        $order = Order::factory()->forTicket(['price' => 4900])->create();

        $this->assertTrue(Money::EUR(4900)->equals($order->total));
    }

    public function test_it_can_process_a_payment()
    {
        $this->markTestSkipped('Implement payment provider fake');
    }
}
