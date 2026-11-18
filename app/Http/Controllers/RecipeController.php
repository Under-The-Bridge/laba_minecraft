<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Item;
use App\Models\Recipe;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with(['resultItem', 'ingredients'])->get();
        return view('recipes.index', compact('recipes'));
    }

    public function create()
    {
        $results = Item::whereIn('type', ['tool', 'weapon', 'armor'])->get();
        $resources = Item::where('type', 'resource')->get();
        return view('recipes.create', compact('results', 'resources'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRecipe($request);

        $recipe = DB::transaction(function () use ($data) {
            $recipe = new Recipe;
            $recipe->name = $data['name'];
            $recipe->result_item_id = $data['result_item_id'];
            $recipe->result_quantity = $data['result_quantity'];
            $recipe->save();

            foreach ($data['ingredients'] as $ingredient) {
                $recipe->ingredients()->attach($ingredient['id'], [
                    'quantity' => $ingredient['quantity'],
                ]);
            }

            return $recipe;
        });

        return redirect('/recipes/'.$recipe->id)->with('success', 'Рецепт создан');
    }

    public function show($id)
    {
        $recipe = Recipe::with(['resultItem', 'ingredients'])->findOrFail($id);
        return view('recipes.show', compact('recipe'));
    }

    public function edit($id)
    {
        $recipe = Recipe::with('ingredients')->findOrFail($id);
        $results = Item::whereIn('type', ['tool', 'weapon', 'armor'])->get();
        $resources = Item::where('type', 'resource')->get();
        return view('recipes.edit', compact('recipe', 'results', 'resources'));
    }

    public function update(Request $request, $id)
    {
        $data = $this->validateRecipe($request);

        DB::transaction(function () use ($data, $id) {
            $recipe = Recipe::lockForUpdate()->findOrFail($id);
            $recipe->name = $data['name'];
            $recipe->result_item_id = $data['result_item_id'];
            $recipe->result_quantity = $data['result_quantity'];
            $recipe->save();

            $ingredients = [];
            foreach ($data['ingredients'] as $ingredient) {
                $ingredients[$ingredient['id']] = ['quantity' => $ingredient['quantity']];
            }

            $recipe->ingredients()->sync($ingredients);
        }, 3);

        return redirect('/recipes/'.$id)->with('success', 'Рецепт сохранён');
    }

    public function destroy($id)
    {
        $deleted = DB::transaction(function () use ($id) {
            $recipe = Recipe::lockForUpdate()->findOrFail($id);

            if ($recipe->craftLogs()->exists()) {
                return false;
            }

            $recipe->delete();
            return true;
        }, 3);

        if (!$deleted) {
            return redirect('/')->with('error', 'Этот рецепт уже использовался в крафте');
        }

        return redirect('/')->with('success', 'Рецепт удалён');
    }

    private function validateRecipe(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'result_item_id' => [
                'required', 'integer',
                Rule::exists('items', 'id')->where(fn ($query) => $query->whereIn('type', ['tool', 'weapon', 'armor'])),
            ],
            'result_quantity' => 'required|integer|min:1|max:4294967295',
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'required|array:id,quantity',
            'ingredients.*.id' => [
                'required', 'integer', 'distinct',
                Rule::exists('items', 'id')->where(fn ($query) => $query->where('type', 'resource')),
            ],
            'ingredients.*.quantity' => 'required|integer|min:1|max:4294967295',
        ], [
            'name.required' => 'Введи название рецепта.',
            'name.max' => 'Название должно быть не длиннее 255 символов.',
            'result_item_id.required' => 'Выбери результат рецепта.',
            'result_item_id.exists' => 'Результатом может быть инструмент, оружие или броня.',
            'result_quantity.*' => 'Введи целое количество от 1 до 4294967295.',
            'ingredients.required' => 'Выбери хотя бы один ресурс.',
            'ingredients.*.id.distinct' => 'Один ресурс нельзя указать дважды.',
            'ingredients.*.id.exists' => 'Ингредиентом может быть только ресурс.',
            'ingredients.*.quantity.*' => 'Введи целое количество от 1 до 4294967295.',
        ]);
    }
}
