<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CraftController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RecipeController::class,'index']);
Route::get('/recipes', [RecipeController::class,'index']);
Route::get('/recipes/{id}', [RecipeController::class,'show'])->whereNumber('id');

Route::middleware('guest')->group(function () {
    Route::view('/register', 'register')->name('register');
    Route::post('/register', [AuthController::class,'register'])->middleware('throttle:10,1');
    Route::get('/login', [AuthController::class,'login_page'])->name('login');
    Route::post('/login', [AuthController::class,'login'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class,'logout']);
    Route::get('/recipes/create', [RecipeController::class,'create']);
    Route::post('/recipes', [RecipeController::class,'store']);
    Route::get('/recipes/{id}/edit', [RecipeController::class,'edit'])->whereNumber('id');
    Route::put('/recipes/{id}', [RecipeController::class,'update'])->whereNumber('id');
    Route::delete('/recipes/{id}', [RecipeController::class,'destroy'])->whereNumber('id');
    Route::post('/craft', [CraftController::class,'store']);
    Route::get('/inventory', [InventoryController::class,'index']);
});
