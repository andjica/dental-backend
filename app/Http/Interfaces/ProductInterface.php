<?php
namespace App\Http\Interfaces;

interface ProductInterface
{
    public function create(array $data);
    public function getOne(int $id);
    public function getByUserId(int $userId);
    public function update(array $data, int $productId);
}