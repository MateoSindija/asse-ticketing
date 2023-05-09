<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tickets>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {


        return [
            "id" => $this->faker->unique()->uuid(),
            "user_id" => User::all()->random()->id,
            "client_id" => Client::all()->random()->id,
            "status" => $this->faker->randomElement(['Open', 'In progress', 'Closed']),
            "title" => $this->faker->word(),
            "description" => $this->faker->text(),
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
}
