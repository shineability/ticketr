<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'quantity' => 1,
            'email' => $this->faker->safeEmail(),
            'status' => 'pending',
        ];
    }

    public function status(string $status): Factory
    {
        return $this->state(function (array $attributes) use ($status) {
            return [
                'status' => $status,
            ];
        });
    }

    public function pending(): Factory
    {
        return $this->status('pending');
    }

    public function canceled(): Factory
    {
        return $this->status('canceled');
    }

    public function completed(): Factory
    {
        return $this->status('completed');
    }
}
