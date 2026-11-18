<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $resources = Item::where('type', 'resource')->get();

        foreach (User::all() as $player) {
            foreach ($resources->random(fake()->numberBetween(5, 10)) as $resource) {
                $player->items()->attach($resource->id, [
                    'quantity' => fake()->numberBetween(10, 100),
                ]);
            }
        }
    }
}
