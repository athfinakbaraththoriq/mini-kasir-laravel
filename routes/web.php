<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

Route::get('/products', function () {
    return 'Daftar Produk';
});

Route::get('/products/{id}', function ($id) {
    return 'Produk ID: ' . $id;
});

Route::post('/products', function () {
    return 'POST BERHASIL';
});

Route::get('/test-error', function () {
    throw new Exception('Terjadi kesalahan!');
});


