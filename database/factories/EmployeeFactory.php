<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        return [
            'name' => fake()->name($gender),
            'team_id' => fake()->numberBetween(1, 40),
            'role_id' => fake()->numberBetween(1, 20),
            'email' => fake()->unique()->safeEmail,
            'gender' => $gender,
            'age' => fake()->numberBetween(18, 65),
            'phone' => fake()->phoneNumber,
            'photo' => fake()->imageUrl(640, 480, 'people', true),
            'is_verified' => true,
            'verified_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
