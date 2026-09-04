<?php

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::get('/products', [ProductController::class, 'index']);

Route::get('/products/{id}', [ProductController::class, 'show']);

Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);

Route::get('/products/quantity/{quantity}', [
    ProductController::class,
    'aboveQuantity'
]);
Route::post('/products/{id}/decrease/{amount}', [
    ProductController::class,
    'decreaseQuantity'
]);

Route::post('/checkout', [OrderController::class, 'checkout']);
Route::middleware(['auth:sanctum', 'admin'])
    ->delete('/products/{id}', [ProductController::class, 'destroy']);

Route::middleware('auth:sanctum')
    ->get('/orders/{id}', [OrderController::class, 'show']);