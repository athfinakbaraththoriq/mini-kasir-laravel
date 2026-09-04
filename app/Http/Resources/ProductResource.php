<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'harga' => $this->harga,
            'quantity' => $this->quantity,

            'categories' => CategoryResource::collection(
                $this->whenLoaded('categories')

            ),
            'pivot' => [
                'quantity' => $this->whenPivotLoaded(
                    'order_product',
                    fn() => $this->pivot->quantity
                ),
                'price' => $this->whenPivotLoaded(
                    'order_product',
                    fn() => $this->pivot->price
                ),
            ],
        ];
    }
}
