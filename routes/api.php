<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TableController;
use Illuminate\Support\Facades\Route;

// public
Route::get('/foods/categories', [FoodController::class, 'getCategories']);
Route::get('/foods', [FoodController::class, 'index']);
Route::get('/foods/{food}', [FoodController::class, 'show']);
Route::get('/tables', [TableController::class, 'index']);
Route::get('/tables/{id}', [TableController::class, 'show']);

// auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // table routes
    Route::apiResource('tables', TableController::class)->only(['index', 'store']);
    Route::post('/tables/{id}/reserve', [TableController::class, 'reserve']);

    // order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{id}/items', [OrderController::class, 'addItem']);

    // Route for kasir role
    Route::middleware('role:kasir')->group(function () {
        Route::post('/tables/{id}/inactive', [TableController::class, 'setInactive']);
        Route::put('/foods/{id}/status', [FoodController::class, 'updateStatus']);
        Route::post('/foods', [FoodController::class, 'store']);
        Route::put('/foods/{food}', [FoodController::class, 'update']);
        Route::delete('/foods/{food}', [FoodController::class, 'destroy']);
    });
});