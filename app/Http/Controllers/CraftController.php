<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\CraftLog;
use App\Models\Inventory;
use App\Models\Recipe;
use App\Models\User;

class CraftController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'recipe_id' => 'required|integer|exists:recipes,id',
        ]);
        $player_id = $request->user()->id;
        $recipe = Recipe::with('ingredients')->findOrFail($data['recipe_id']);
        $inventory = Inventory::where('player_id', $player_id)->get()->keyBy('item_id');
        $this->checkResources($recipe, $inventory);

        DB::transaction(function () use ($recipe, $player_id) {
            $recipe = Recipe::lockForUpdate()->findOrFail($recipe->id);
            User::lockForUpdate()->findOrFail($player_id);
            $inventory = Inventory::where('player_id', $player_id)->lockForUpdate()->get()->keyBy('item_id');

            $this->checkResources($recipe, $inventory);

            foreach ($recipe->ingredients as $ingredient) {
                $item = $inventory[$ingredient->id];
                $item->quantity -= $ingredient->pivot->quantity;
                $item->save();
            }

            $result = $inventory->get($recipe->result_item_id) ?? new Inventory;
            $result->player_id = $player_id;
            $result->item_id = $recipe->result_item_id;
            $result->quantity += $recipe->result_quantity;

            if ($result->quantity > 4294967295) {
                throw ValidationException::withMessages(['recipe_id' => 'В инвентаре уже слишком много таких предметов']);
            }

            $result->save();

            $log = new CraftLog;
            $log->player_id = $player_id;
            $log->recipe_id = $recipe->id;
            $log->save();
        }, 3);

        return redirect('/inventory')->with('success', 'Предмет создан');
    }

    private function checkResources($recipe, $inventory)
    {
        if ($recipe->ingredients->isEmpty()) {
            throw ValidationException::withMessages(['recipe_id' => 'У рецепта нет ингредиентов']);
        }

        foreach ($recipe->ingredients as $ingredient) {
            if (($inventory->get($ingredient->id)->quantity ?? 0) < $ingredient->pivot->quantity) {
                throw ValidationException::withMessages([
                    'recipe_id' => 'Не хватает ресурса: '.$ingredient->name,
                ]);
            }
        }
    }
}
