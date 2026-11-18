<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\CraftLog;
use App\Models\Item;
use App\Models\Recipe;
use App\Models\User;
use Database\Seeders\ItemSeeder;
use Tests\TestCase;

class CraftTest extends TestCase
{
    use RefreshDatabase;

    private $player;
    private $iron;
    private $wood;
    private $sword;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ItemSeeder::class);
        $this->player = User::factory()->create();
        $this->iron = Item::where('slug', 'iron')->firstOrFail();
        $this->wood = Item::where('slug', 'wood')->firstOrFail();
        $this->sword = Item::where('slug', 'sword')->firstOrFail();
        $this->actingAs($this->player);
    }

    public function test_craft_consumes_resources_and_adds_the_result(): void
    {
        $recipe = $this->recipe();
        $this->player->items()->attach($this->iron->id, ['quantity' => 10]);
        $other = User::factory()->create();
        $other->items()->attach($this->iron->id, ['quantity' => 9]);

        for ($i = 1; $i <= 2; $i++) {
            $this->post('/craft', ['recipe_id' => $recipe->id])
                ->assertRedirect('/inventory')
                ->assertSessionHas('success', 'Предмет создан');

            $this->assertSame(10 - 3 * $i, $this->quantity($this->iron));
            $this->assertSame($i, $this->quantity($this->sword));
        }

        $this->assertSame(9, $other->items()->first()->pivot->quantity);
        $this->assertDatabaseCount('craft_logs', 2);
        $this->assertDatabaseHas('craft_logs', ['player_id' => $this->player->id, 'recipe_id' => $recipe->id]);
        $this->get('/inventory')->assertOk()->assertSee($this->sword->name);
    }

    public function test_craft_with_missing_resources_leaves_inventory_unchanged(): void
    {
        $recipe = $this->recipe();
        $recipe->ingredients()->attach($this->wood->id, ['quantity' => 1]);
        $this->player->items()->attach($this->iron->id, ['quantity' => 4]);

        $this->from('/recipes/'.$recipe->id)->post('/craft', ['recipe_id' => $recipe->id])
            ->assertRedirect('/recipes/'.$recipe->id)
            ->assertSessionHasErrors('recipe_id');

        $this->assertSame(4, $this->quantity($this->iron));
        $this->assertNull($this->quantity($this->sword));
        $this->assertDatabaseCount('craft_logs', 0);
    }

    public function test_craft_rolls_back_spending_when_the_result_cannot_be_added(): void
    {
        $recipe = $this->recipe();
        $this->player->items()->attach([
            $this->iron->id => ['quantity' => 4],
            $this->sword->id => ['quantity' => 4294967295],
        ]);

        $this->post('/craft', ['recipe_id' => $recipe->id])->assertSessionHasErrors('recipe_id');

        $this->assertSame(4, $this->quantity($this->iron));
        $this->assertSame(4294967295, $this->quantity($this->sword));
        $this->assertDatabaseCount('craft_logs', 0);
    }

    public function test_duplicate_ingredients_are_rejected(): void
    {
        $data = $this->recipeData();
        $data['ingredients'][] = ['id' => $this->iron->id, 'quantity' => 2];

        $this->post('/recipes', $data)->assertSessionHasErrors('ingredients.1.id');
        $this->assertDatabaseCount('recipes', 0);
    }

    public function test_a_resource_cannot_be_the_recipe_result(): void
    {
        $data = $this->recipeData();
        $data['result_item_id'] = $this->wood->id;

        $this->post('/recipes', $data)->assertSessionHasErrors('result_item_id');
        $this->assertDatabaseCount('recipes', 0);
    }

    public function test_equipment_cannot_be_an_ingredient_and_quantities_must_be_positive(): void
    {
        $data = $this->recipeData();
        $data['result_quantity'] = 0;
        $data['ingredients'] = [['id' => $this->sword->id, 'quantity' => 0]];

        $this->post('/recipes', $data)->assertSessionHasErrors([
            'result_quantity', 'ingredients.0.id', 'ingredients.0.quantity',
        ]);
    }

    public function test_a_used_recipe_cannot_be_deleted(): void
    {
        $recipe = $this->recipe();
        CraftLog::factory()->create(['player_id' => $this->player->id, 'recipe_id' => $recipe->id]);

        $this->delete('/recipes/'.$recipe->id)->assertRedirect('/')->assertSessionHas('error');
        $this->assertModelExists($recipe);
        $this->assertDatabaseCount('recipe_ingredient', 1);
    }

    public function test_recipe_can_be_created_edited_and_deleted_before_crafting(): void
    {
        $this->get('/recipes/create')->assertOk()->assertSee('Создать рецепт');
        $response = $this->post('/recipes', $this->recipeData());
        $recipe = Recipe::firstOrFail();
        $response->assertRedirect('/recipes/'.$recipe->id)->assertSessionHas('success');
        $this->get('/')->assertOk()->assertSee($recipe->name);
        $this->get('/recipes/'.$recipe->id)->assertOk()->assertSee($this->iron->name);
        $this->get('/recipes/'.$recipe->id.'/edit')->assertOk()->assertSee($recipe->name);

        $data = $this->recipeData();
        $data['name'] = 'Новый рецепт';
        $data['ingredients'] = [['id' => $this->wood->id, 'quantity' => 2]];

        $this->put('/recipes/'.$recipe->id, $data)->assertRedirect('/recipes/'.$recipe->id);
        $this->assertSame($data['name'], $recipe->fresh()->name);
        $this->assertSame([$this->wood->id], $recipe->ingredients()->pluck('items.id')->all());
        $this->assertSame(2, $recipe->ingredients()->first()->pivot->quantity);

        $this->delete('/recipes/'.$recipe->id)->assertRedirect('/')->assertSessionHas('success');
        $this->assertModelMissing($recipe);
        $this->assertDatabaseCount('recipe_ingredient', 0);
    }

    private function recipeData()
    {
        return [
            'name' => 'Крафт меча',
            'result_item_id' => $this->sword->id,
            'result_quantity' => 1,
            'ingredients' => [['id' => $this->iron->id, 'quantity' => 3]],
        ];
    }

    private function recipe()
    {
        $recipe = Recipe::factory()->create(['result_item_id' => $this->sword->id, 'result_quantity' => 1]);
        $recipe->ingredients()->attach($this->iron->id, ['quantity' => 3]);
        return $recipe;
    }

    private function quantity(Item $item)
    {
        return $this->player->items()->where('items.id', $item->id)->first()?->pivot->quantity;
    }
}
