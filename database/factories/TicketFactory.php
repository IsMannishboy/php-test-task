<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(), 
            'topic' => $this->faker->sentence(3),
            'text' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['new', 'pending', 'done']),
            'response' => null,
        ];
    }
}