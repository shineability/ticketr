<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Organizer;
use App\Payment\Contracts\PaymentProvider;
use App\Payment\PaymentProviderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use InvalidArgumentException;
use Tests\TestCase;

class PaymentProviderFactoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_creates_a_payment_provider()
    {
        $provider = $this->mock(PaymentProvider::class);

        $factory = app(PaymentProviderFactory::class);

        $factory->extend('fake', function ($app) use ($provider) {
            return $provider;
        });

        $organizer = Organizer::factory()
            ->hasTickets(1)
            ->create(['payment_provider' => 'fake']);

        $order = Order::factory()
            ->for($organizer->tickets->first())
            ->create();

        $this->assertSame($provider, $factory->create('fake'));
        $this->assertSame($provider, $factory->createForOrganizer($organizer));
        $this->assertSame($provider, $factory->createForOrder($order));
    }

    public function test_it_cannot_create_an_unregistered_payment_provider()
    {
        $factory = app(PaymentProviderFactory::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment provider [foobar] not supported');

        $factory->create('foobar');
    }
}
