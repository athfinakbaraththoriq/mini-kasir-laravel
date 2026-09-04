<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Helpers\ApiResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = $this->orderService->checkout(
            $validated['items']
        );

        return ApiResponse::success(
            'Checkout berhasil',
            new OrderResource($order),
            201
        );
    }
    public function show(int $id)
    {
        $order = \App\Models\Order::with('products')->findOrFail($id);

        return \App\Helpers\ApiResponse::success(
            'Data order berhasil diambil',
            new OrderResource($order)
        );
    }
}
