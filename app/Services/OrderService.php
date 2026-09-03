<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function checkout(array $items)
    {
        return DB::transaction(function () use ($items) {

            $total = 0;
            $products = [];

            // 1. Cek semua produk dan stok
            foreach ($items as $item) {

                $product = $this->productRepository
                    ->findById($item['product_id']);

                if (!$product) {
                    throw new \Exception(
                        "Produk dengan ID {$item['product_id']} tidak ditemukan."
                    );
                }

                if ($product->quantity < $item['quantity']) {
                    throw new \Exception(
                        "Stok {$product->nama} tidak mencukupi."
                    );
                }

                $subtotal = $product->harga * $item['quantity'];

                $total += $subtotal;

                $products[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->harga,
                ];
            }

            // 2. Buat order
            $order = Order::create([
                'invoice' => 'INV-' . time(),
                'total' => $total,
            ]);

            // 3. Masukkan produk + kurangi stok
            foreach ($products as $item) {

                $order->products()->attach(
                    $item['product']->id,
                    [
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]
                );

                $item['product']->quantity -= $item['quantity'];

                $this->productRepository->save(
                    $item['product']
                );
            }

            return $order->load('products');
        });
    }
}