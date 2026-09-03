<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = [
        'nama',
        'harga',
        'quantity',
    ];
    protected function nama(): Attribute
    {
        return Attribute::make(
            get: fn($value) => strtoupper($value),

            set: fn($value) => ucwords($value),
        );
    }
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopePriceAbove(Builder $query, int $price): Builder
    {
        return $query->where('harga', '>', $price);
    }
    public function scopeQuantityAbove(Builder $query, int $quantity): Builder
    {
        return $query->where('quantity', '>', $quantity);
    }
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity', 'price');
    }
}
