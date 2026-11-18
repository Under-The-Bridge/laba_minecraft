<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CraftLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'player_id' => User::factory(),
            'recipe_id' => Recipe::factory(),
            'created_at' => fake()->dateTimeBetween('-1 year', '-1 minute'),
        ];
    }
}
