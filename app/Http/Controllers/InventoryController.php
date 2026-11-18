<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $player = $request->user();
        $items = $player->items()->wherePivot('quantity', '>', 0)->orderBy('name')->get();
        return view('inventory.index', compact('player', 'items'));
    }
}
