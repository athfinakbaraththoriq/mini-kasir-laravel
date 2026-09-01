<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Repositories\ProductRepository;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepository $productRepository
    ) {}

    public function index()
    {
        $products = $this->productRepository->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil',
            'data' => $products
        ]);
    }

    public function show(int $id)
    {
        $product = $this->productRepository->findById($id);

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

        $product = $this->productRepository->create($validated);

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

        $product = $this->productRepository->update($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'data' => $product
        ]);
    }
    public function destroy(int $id)
    {
        $product = $this->productRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus',
            'data' => null
        ]);
    }
}
