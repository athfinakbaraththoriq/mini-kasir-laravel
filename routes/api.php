<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;


Route::get('/products', [ProductController::class, 'index']);

Route::get('/products/{id}', [ProductController::class, 'show']);

Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::get('/products/quantity/{quantity}', [
    ProductController::class,
    'aboveQuantity'
]);
Route::post('/products/{id}/decrease/{amount}', [
    ProductController::class,
    'decreaseQuantity'
]);

Route::post('/checkout', [OrderController::class, 'checkout']);