<?php

namespace Tests\Feature;

use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_a_formatted_price()
    {
        $ticket = Ticket::factory()->create(['price' => 4900]);

        $this->assertEquals("€ 49,00", $ticket->formattedPrice);
    }
}
