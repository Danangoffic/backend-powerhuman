<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyUser>
 */
class CompanyUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $id = fake()->unique()->numberBetween(1, 10);
        $user_data = User::with('team')->where('id', fake()->unique()->numberBetween(1, 10))->first();
        $team_data = $user_data->team;
        $company_id = $team_data->company_id;
        return [
            'user_id' => $user_data->id,
            'company_id' => $company_id,
        ];
    }
}
