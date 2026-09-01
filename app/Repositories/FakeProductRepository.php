<?php

namespace App\Repositories;

class FakeProductRepository implements ProductRepositoryInterface
{
    public function getAll()
    {
        return collect([
            [
                'id' => 999,
                'nama' => 'Fake Product',
                'harga' => 123456,
                'quantity' => 99,
            ]
        ]);
    }

    public function findById(int $id)
    {
        return null;
    }

    public function create(array $data)
    {
        return $data;
    }

    public function update(int $id, array $data)
    {
        return $data;
    }

    public function delete(int $id)
    {
        return true;
    }
}