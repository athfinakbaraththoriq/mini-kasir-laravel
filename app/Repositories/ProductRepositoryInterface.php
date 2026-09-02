<?php

namespace App\Repositories;
use App\Models\Product;

interface ProductRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getAboveQuantity(int $quantity);
    public function save(Product $product);
}