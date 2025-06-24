<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Interfaces\ProductInterface;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{

    protected ProductInterface $productService;

    public function __construct(ProductInterface $productService)
    {
        $this->productService = $productService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {

        
        $product = $this->productService->create($request->validated());

        return response()->json([
            'success' => 'Product created!',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $product = $this->productService->getOne($id);

        if(is_null($product))
        {
             return response()->json([
            'message' => 'Product doesnt exist',
        ], 404);
        }

        $product->load(['category', 'subCategory', 'images']);

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully.',
            'data' => $product
        ], 200);
    }

    public function getByUserId($userId)
    {
        $products = $this->productService->getByUserId($userId);

        if(is_null($products))
        {
            return response()->json([
            'message' => 'Products doesnt exist',
        ], 404);
        }

        $products->load(['category', 'subCategory']);

          return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully.',
            'data' => $products
        ], 200);
 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(array $data, int $productId)
    {
        $updatedProduct = $this->productService->update($data, $productId);

        if (!$updatedProduct) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($updatedProduct, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
