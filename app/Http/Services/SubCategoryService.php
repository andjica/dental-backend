<?php
namespace App\Http\Services;

use App\Http\Interfaces\SubCategoryInterface;
use App\Models\SubCategory;

class SubCategoryService implements SubCategoryInterface
{
    public function getByCategoryId(int $categoryId)
    {
        $subCategories = SubCategory::where('category_id', $categoryId)->get();

        return $subCategories;
    }
}