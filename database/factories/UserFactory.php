<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */




    public function definition(): array
    {

        $hashedPassword = Hash::make("password");

        return [
            "id" => $this->faker->unique()->uuid(),
            "first_name" => $this->faker->firstName(),
            "last_name" => $this->faker->lastName(),
            "email" => $this->faker->unique()->email(),
            "password" => $hashedPassword,
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
}
