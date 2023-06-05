<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comments>
 */
class ReplyFactory extends Factory
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
            "comment_id" => Comment::all()->random()->id,
            "user_id" => User::all()->random()->id,
            "reply" => $this->faker->text(),
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
}
