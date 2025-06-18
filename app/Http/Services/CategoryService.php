<?php
namespace App\Http\Services;

use App\Models\Category;
use App\Http\Interfaces\CategoryInterface;

class CategoryService implements CategoryInterface
{
    public function getAll()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return $categories;
    }
}