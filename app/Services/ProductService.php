<?php

namespace App\Services;

use App\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Exceptions\InsufficientStockException;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function getAll()
    {
        return $this->productRepository->getAll();
    }

    public function findById(int $id)
    {
        return $this->productRepository->findById($id);
    }

    public function create(array $data)
    {
        if ($data['quantity'] < 0) {
            throw new \InvalidArgumentException(
                'Quantity tidak boleh kurang dari 0.'
            );
        }

        return $this->productRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->productRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->productRepository->delete($id);
    }
    public function getProductsAboveQuantity(int $quantity)
    {
        return $this->productRepository->getAboveQuantity($quantity);
    }
    public function decreaseQuantity(int $id, int $amount)
    {
        return DB::transaction(function () use ($id, $amount) {
            $product = $this->productRepository->findById($id);

            if (!$product) {
                throw new \Exception('Produk tidak ditemukan.');
            }

            if ($product->quantity < $amount) {
                throw new InsufficientStockException('Stock tidak mencukupi.');
            }

            $product->quantity -= $amount;

            $this->productRepository->save($product);

            return $product;
        });
    }
}
