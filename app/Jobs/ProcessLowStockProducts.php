<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessLowStockProducts implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        Product::where('quantity', '<=', 5)
            ->chunk(5, function ($products) {
                foreach ($products as $product) {
                    logger()->info(
                        "Job memproses produk: {$product->nama} | Stok: {$product->quantity}"
                    );
                }
            });
    }
}