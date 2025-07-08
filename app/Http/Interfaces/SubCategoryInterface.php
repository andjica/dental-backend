<?php
namespace App\Http\Interfaces;

interface SubCategoryInterface
{
    public function getByCategoryId(int $categoryId);
    public function getAll();
    public function create(array $data);
    public function getById(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
}