<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'result_item_id' => Item::factory()->equipment(),
            'result_quantity' => fake()->numberBetween(1, 3),
        ];
    }
}
