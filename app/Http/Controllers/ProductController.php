<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\Gate;
use App\Helpers\ApiResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index()
    {
        $products = $this->productService->getAll();

        return ApiResponse::success(
            'Data produk berhasil diambil',
            $products
        );
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
        $data = $request->validate([
            'nama' => 'required|string',
            'harga' => 'required|numeric|min:1',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = $this->productService->create($data);

        return ApiResponse::success(
            'Produk berhasil dibuat',
            $product,
            201
        );
    }
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'harga' => 'required|numeric|min:1',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = $this->productService->update($id, $data);

        return ApiResponse::success(
            'Produk berhasil diupdate',
            $product
        );
    }
    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);

        Gate::authorize('delete', $product);

        $this->productService->delete($id);

        return ApiResponse::success(
            'Produk berhasil dihapus'
        );
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
    public function decreaseQuantity(int $id, int $amount)
    {
        $product = $this->productService->decreaseQuantity($id, $amount);

        return response()->json([
            'success' => true,
            'message' => 'Quantity berhasil dikurangi',
            'data' => $product
        ]);
    }
}
