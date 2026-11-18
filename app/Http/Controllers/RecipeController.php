<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index(){
        $recipe = Recipe::all();
        return view('index',compact('recipe'));
    }
}
