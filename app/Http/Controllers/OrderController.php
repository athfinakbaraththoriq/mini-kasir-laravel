<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

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

        return response()->json([
            'success' => true,
            'message' => 'Checkout berhasil',
            'data' => $order,
        ], 201);
    }
}