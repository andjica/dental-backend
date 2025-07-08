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
}