<?php
namespace App\Http\Interfaces;

use Illuminate\Http\Request;

interface ProductInterface
{
    public function create(array $data);
    public function getOne(int $id);
    public function getByUserId(int $userId);
    public function update(Request $request, int $productId);
    public function getAll();
}