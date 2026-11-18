<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public $items = [
        ['name' => 'Железо', 'slug' => 'iron', 'type' => 'resource'],
        ['name' => 'Дерево', 'slug' => 'wood', 'type' => 'resource'],
        ['name' => 'Камень', 'slug' => 'stone', 'type' => 'resource'],
        ['name' => 'Кожа', 'slug' => 'leather', 'type' => 'resource'],
        ['name' => 'Ткань', 'slug' => 'cloth', 'type' => 'resource'],
        ['name' => 'Уголь', 'slug' => 'coal', 'type' => 'resource'],
        ['name' => 'Медь', 'slug' => 'copper', 'type' => 'resource'],
        ['name' => 'Золото', 'slug' => 'gold', 'type' => 'resource'],
        ['name' => 'Серебро', 'slug' => 'silver', 'type' => 'resource'],
        ['name' => 'Верёвка', 'slug' => 'rope', 'type' => 'resource'],
        ['name' => 'Кирка', 'slug' => 'pickaxe', 'type' => 'tool'],
        ['name' => 'Топор', 'slug' => 'axe', 'type' => 'tool'],
        ['name' => 'Лопата', 'slug' => 'shovel', 'type' => 'tool'],
        ['name' => 'Молоток', 'slug' => 'hammer', 'type' => 'tool'],
        ['name' => 'Серп', 'slug' => 'sickle', 'type' => 'tool'],
        ['name' => 'Меч', 'slug' => 'sword', 'type' => 'weapon'],
        ['name' => 'Лук', 'slug' => 'bow', 'type' => 'weapon'],
        ['name' => 'Копьё', 'slug' => 'spear', 'type' => 'weapon'],
        ['name' => 'Шлем', 'slug' => 'helmet', 'type' => 'armor'],
        ['name' => 'Нагрудник', 'slug' => 'chestplate', 'type' => 'armor'],
    ];

    public function definition(): array
    {
        return fake()->unique()->randomElement($this->items);
    }

    public function resource(): static
    {
        return $this->state(fn (array $attributes) => fake()->unique()->randomElement(
            array_values(array_filter($this->items, fn ($item) => $item['type'] === 'resource'))
        ));
    }

    public function equipment(): static
    {
        return $this->state(fn (array $attributes) => fake  ()->unique()->randomElement(
            array_values(array_filter($this->items, fn ($item) => $item['type'] !== 'resource'))
        ));
    }
}
