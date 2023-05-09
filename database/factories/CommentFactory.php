<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comments>
 */
class CommentFactory extends Factory
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
            "ticket_id" => Ticket::all()->random()->id,
            "user_id" => User::all()->random()->id,
            "comment" => $this->faker->text(),
            "created_at" => now(),
        ];
    }
}
