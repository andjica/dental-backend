<?php
namespace App\Http\Interfaces;

interface SubCategoryInterface
{
    public function getByCategoryId(int $categoryId);
    public function getAll();

}