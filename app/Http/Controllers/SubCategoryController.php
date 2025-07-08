<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Interfaces\SubCategoryInterface;

class SubCategoryController extends Controller
{
   protected $subCategoryService;

    public function __construct(SubCategoryInterface $subCategoryService)
    {
        $this->subCategoryService = $subCategoryService;
    }

    public function index()
    {
        $subcategories = $this->subCategoryService->getAll();
        $subcategories->load(['category']);

        return response()->json(['subcategories' => $subcategories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $created = $this->subCategoryService->create($validated);

        return response()->json($created, 201);
    }

    public function show($id)
    {
        return response()->json($this->subCategoryService->getById($id));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $updated = $this->subCategoryService->update($id, $validated);

        return response()->json($updated);
    }

    public function destroy($id)
    {
        $this->subCategoryService->delete($id);
        return response()->json(['message' => 'Subcategory deleted successfully']);
    }
}
