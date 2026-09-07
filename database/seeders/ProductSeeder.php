<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::query()->delete();

        $products = [
            [
                'nama' => 'Indomie Goreng',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Indomie Goreng Rendang',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Indomie Goreng Aceh',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Indomie Goreng Ayam kriuk',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Indomie Goreng Sambal Matah',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Mie Sedap Goreng',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Mie Sedap Goreng Chef Devina',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Mie Sedap Goreng Ayam Geprek',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Mie Sedap Kuah Rasa Soto',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Mie Sedap Kuah Rasa Ayam Bawang',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Mie Sedap Kuah Rasa Ayam',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Indomie Kuah Rasa Soto',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Indomie Kuah Rasa Kari Ayam',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Indomie Kuah Rasa Ayam Bawang',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Indomie Kuah Rasa Soto Lamongan',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Indomie Kuah Rasa Soto Madura',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Indomie Nyemek Jogja',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Indomie Nyemek Bangladesh',
                'harga' => 3000,
                'quantity' => 20,
            ],
            [
                'nama' => 'Aqua',
                'harga' => 4000,
                'quantity' => 15,
            ],
            [
                'nama' => 'Teh Sosro',
                'harga' => 4000,
                'quantity' => 15,
            ],
            [
                'nama' => 'Teh Kotak',
                'harga' => 4000,
                'quantity' => 15,
            ],
            [
                'nama' => 'Teh Botol',
                'harga' => 5000,
                'quantity' => 12,
            ],
            [
                'nama' => 'Sprite',
                'harga' => 4000,
                'quantity' => 15,
            ],
            [
                'nama' => 'Coca Cola',
                'harga' => 4000,
                'quantity' => 15,
            ],
            [
                'nama' => 'Pepsi',
                'harga' => 4000,
                'quantity' => 15,
            ],
            [
                'nama' => 'Big Cola',
                'harga' => 4000,
                'quantity' => 15,
            ],
            [
                'nama' => 'Roti Isi krim',
                'harga' => 4000,
                'quantity' => 10,
            ],
            [
                'nama' => 'Kopi Hitam',
                'harga' => 3000,
                'quantity' => 8,
            ],
            [
                'nama' => 'Kopi Gula Aren',
                'harga' => 4000,
                'quantity' => 8,
            ],
            [
                'nama' => 'Kopi Hitam Kapal Api',
                'harga' => 3000,
                'quantity' => 8,
            ],
            [
                'nama' => 'Kopi Susu',
                'harga' => 5000,
                'quantity' => 8,
            ],
            [
                'nama' => 'Kopikap',
                'harga' => 1000,
                'quantity' => 8,
            ],
            [
                'nama' => 'Susu',
                'harga' => 5000,
                'quantity' => 8,
            ],
            [
                'nama' => 'Es Jeruk',
                'harga' => 4000,
                'quantity' => 8,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
