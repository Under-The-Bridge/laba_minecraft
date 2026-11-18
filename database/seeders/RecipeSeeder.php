<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Recipe;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $resources = Item::where('type', 'resource')->get();
        $results = Item::whereIn('type', ['tool', 'weapon', 'armor'])->get();

        foreach ($results as $item) {
            $recipe = Recipe::factory()->create(['result_item_id' => $item->id]);

            foreach ($resources->random(fake()->numberBetween(2, 4)) as $resource) {
                $recipe->ingredients()->attach($resource->id, [
                    'quantity' => fake()->numberBetween(1, 10),
                ]);
            }
        }
    }
}
