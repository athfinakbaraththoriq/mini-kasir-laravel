<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/products', function () {
    return 'Daftar Produk';
});

Route::get('/products/{id}', function ($id) {
    return 'Produk ID: ' . $id;
});

Route::post('/products', function () {
    return 'POST BERHASIL';
});

