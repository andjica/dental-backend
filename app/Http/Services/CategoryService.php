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

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update($id, array $data)
    {
        $category = Category::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function show($id)
    {
        return Category::findOrFail($id);
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        return $category->delete();
    }
}