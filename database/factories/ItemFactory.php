<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public const ITEMS = [
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
        return $this->itemAttributes(self::ITEMS);
    }

    public function resource(): static
    {
        $items = array_filter(self::ITEMS, fn ($item) => $item['type'] === 'resource');
        return $this->state(fn () => $this->itemAttributes($items));
    }

    public function equipment(): static
    {
        $items = array_filter(self::ITEMS, fn ($item) => $item['type'] !== 'resource');
        return $this->state(fn () => $this->itemAttributes($items));
    }

    private function itemAttributes(array $items): array
    {
        $item = null;
        $pick = function () use (&$item, $items) {
            return $item ??= fake()->unique()->randomElement($items);
        };

        return [
            'name' => fn () => $pick()['name'],
            'slug' => fn () => $pick()['slug'],
            'type' => fn () => $pick()['type'],
        ];
    }
}
