<?php
namespace App\Http\Services;

use App\Models\Category;
use App\Models\SubCategory;
use App\Http\Interfaces\SubCategoryInterface;

class SubCategoryService implements SubCategoryInterface
{
    public function getByCategoryId(int $categoryId)
    {

        $category = Category::find($categoryId);

        if (!$category) {
            return null;
        }
        $subCategories = SubCategory::where('category_id', $categoryId)->get();

        return $subCategories;
    }

    public function getAll()
    {
        return SubCategory::orderBy('name', 'asc')->get();
    }

    public function create(array $data)
    {
        return SubCategory::create($data);
    }

    public function getById(int $id)
    {
        return SubCategory::with('category')->findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->update($data);
        return $subCategory;
    }

    public function delete(int $id)
    {
        $subCategory = SubCategory::findOrFail($id);
        return $subCategory->delete();
    }
}