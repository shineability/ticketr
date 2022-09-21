<?php

namespace Tests\Feature;

use App\Payment\PaymentResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Fake\FakePayment;
use Tests\TestCase;

class PaymentResponseTest extends TestCase
{
    use WithFaker;

    public function test_it_can_be_instantiated()
    {
        $payment = FakePayment::completed();
        $checkoutUrl = $this->faker->url();

        $response = PaymentResponse::make($payment, $checkoutUrl);

        $this->assertSame($payment, $response->payment());
        $this->assertEquals($checkoutUrl, $response->checkoutUrl());
    }
}
