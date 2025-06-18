<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Interfaces\CategoryInterface;
use App\Http\Services\SubCategoryService;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $subCategoryService;

    public function __construct(CategoryInterface $categoryService, SubCategoryService $subCategoryService)
    {
        $this->categoryService = $categoryService;
        $this->subCategoryService = $subCategoryService;
    }

   public function index()
    {
        $categories = $this->categoryService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Categories exist',
            'data' => $categories,
        ], 200);
    }

    public function getSubCategories($categoryId)
    {
        $subCategories = $this->subCategoryService->getByCategoryId($categoryId);

        if (is_null($subCategories)) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Category doesnt exists',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subcategories exist',
            'data' => $subCategories,
        ], 200);
    }
}
