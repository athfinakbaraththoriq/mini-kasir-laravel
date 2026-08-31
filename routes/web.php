<?php

use Illuminate\Support\Facades\Route;

Route::get('/products', function () {
    return 'Daftar Produk';
});

Route::get('/products/{id}', function ($id) {
    return 'Produk ID: ' . $id;
});

Route::post('/products', function () {
    return 'POST BERHASIL';
});