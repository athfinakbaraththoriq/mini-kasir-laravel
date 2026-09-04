<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_products(): void
    {
        $user = User::factory()->create();

        Product::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/products');

        $response->assertStatus(200);
    }
    public function test_authenticated_user_can_create_product(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/products', [
                'nama' => 'Kopi Susu',
                'harga' => 15000,
                'quantity' => 10,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'harga' => 15000,
            'quantity' => 10,
        ]);
    }
    public function test_product_creation_fails_with_invalid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/products', [
                'nama' => '',
                'harga' => 0,
                'quantity' => -1,
            ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'nama',
            'harga',
            'quantity',
        ]);
    }
    public function test_admin_can_delete_product(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
    public function test_regular_user_cannot_delete_product(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $product = Product::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }
    public function test_product_cannot_be_decreased_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'quantity' => 5,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/products/{$product->id}/decrease/10");

        $response->assertStatus(422);

        $response->assertJson([
            'success' => false,
            'message' => 'Stock tidak mencukupi.',
            'data' => null,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'quantity' => 5,
        ]);
    }
}
