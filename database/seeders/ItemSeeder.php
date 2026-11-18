<?php

namespace Database\Seeders;

use App\Models\Item;
use Database\Factories\ItemFactory;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ItemFactory::ITEMS as $item) {
            Item::firstOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
