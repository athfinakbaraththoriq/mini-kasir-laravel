<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index()
    {
        $products = $this->productService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil',
            'data' => $products
        ]);
    }

    public function show(int $id)
    {
        $product = $this->productService->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditemukan',
            'data' => $product
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric|min:1',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = $this->productService->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dibuat',
            'data' => $product
        ], 201);
    }
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric|min:1',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = $this->productService->update($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'data' => $product
        ]);
    }
    public function destroy(int $id)
    {
        $product = $this->productService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus',
            'data' => null
        ]);
    }
    public function aboveQuantity(int $quantity)
    {
        $products = $this->productService
            ->getProductsAboveQuantity($quantity);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diambil',
            'data' => $products
        ]);
    }
}
