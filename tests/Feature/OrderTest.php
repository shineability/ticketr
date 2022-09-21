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
use RuntimeException;
use Tests\Fake\FakePayment;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_can_create_a_pending_order()
    {
        $ticket = Ticket::factory()->create();
        $email = $this->faker->safeEmail();

        $order = Order::pending($ticket, $email);

        $this->assertTrue($order->isPending());

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

        $this->assertTrue($order->isCompleted());

        $this->assertDatabaseHas($order, [
            'id' => $order->id,
            'status' => 'completed'
        ]);

        Event::assertDispatched(OrderCompleted::class);
    }

    public function test_it_can_only_be_completed_when_status_is_pending()
    {
        $order = Order::factory()->completed()->create();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Only `pending` orders can be completed');

        $order->complete();
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

        $this->assertTrue($order->isCanceled());

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

    public function test_it_has_a_reference()
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
        $order = Order::factory()->pending()->create();
        $payment = FakePayment::withStatus('foobar');

        $order->processPayment($payment);

        $this->assertDatabaseHas($order, [
            'id' => $order->id,
            'status' => 'pending',
            'payment_transaction_id' => $payment->transactionId(),
            'payment_status' => $payment->status()
        ]);
    }

    public function test_it_can_be_completed_by_processing_a_payment()
    {
        $order = Order::factory()->pending()->create();
        $payment = FakePayment::completed();

        $order->processPayment($payment);

        $this->assertDatabaseHas($order, [
            'id' => $order->id,
            'status' => 'completed',
            'payment_transaction_id' => $payment->transactionId(),
            'payment_status' => $payment->status()
        ]);
    }

    public function test_it_can_be_canceled_by_processing_a_payment()
    {
        $order = Order::factory()->pending()->create();
        $payment = FakePayment::canceled();

        $order->processPayment($payment);

        $this->assertDatabaseHas($order, [
            'id' => $order->id,
            'status' => 'canceled',
            'payment_transaction_id' => $payment->transactionId(),
            'payment_status' => $payment->status()
        ]);
    }
}
