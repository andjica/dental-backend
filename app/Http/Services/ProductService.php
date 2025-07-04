<?php

namespace App\Http\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Interfaces\ProductInterface;
use Illuminate\Http\Request;

class ProductService implements ProductInterface
{
    public function create(array $data)
    {
        $userId = Auth::id();
        $data['user_id'] = $userId;

        // Ako SKU nije poslat – generiši
        if (empty($data['sku'])) {
            $data['sku'] = strtoupper(Str::random(10));
        }
        $data['base_price'] = number_format((float) $data['base_price'], 2, '.', '');


        if ($data['in_stock'] == 1) {
            $data['in_stock'] = 1;
        } else {
            $data['in_stock'] = 0;
        }




        // Odvojimo slike ako postoje
        $images = $data['images'] ?? [];
        unset($data['images']);

        // Kreiraj proizvod
        $product = Product::create($data);

        if (isset($data['main_image'])) {
            $mainImage = $data['main_image'];
            $pathMain = $mainImage->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $pathMain,
                'is_primary' => true,
            ]);
        }

        // Snimi slike ako postoje
        foreach ($images as $image) {
            $path = $image->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $path,
            ]);
        }

        return $product->load('images'); // Vrati i slike u odgovoru

    }

    public function getOne(int $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return null;
        }

        return $product;
    }

    public function getByUserId(int $userId)
    {
        $products = Product::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        return $products;
    }



  public function update(Request $request, int $productId)
  {
    
    $product = Product::findOrFail($productId);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    // ✅ Validacija (možeš proširiti po potrebi)
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'product_type' => 'required|string|in:new,used',
        'description' => 'nullable|string',
        'category_id' => 'required|integer|exists:categories,id',
        'sub_category_id' => 'required|integer|exists:sub_categories,id',
        'base_price' => 'required|numeric',
        'length' => 'nullable|numeric',
        'width' => 'nullable|numeric',
        'height' => 'nullable|numeric',
        'weight' => 'nullable|numeric',
        'quantity' => 'nullable|integer',
        'in_stock' => 'nullable|boolean',
        'image_main' => 'nullable|image',
        'images.*' => 'nullable|image',
        'existing_images' => 'nullable|array',
        'existing_images.*' => 'string',
    ]);

    // ✅ Obrada osnovnih podataka
    $validated['base_price'] = number_format((float) $validated['base_price'], 2, '.', '');
    $validated['in_stock'] = ($request->in_stock == 1) ? 1 : 0;

    if (empty($product->sku)) {
        $validated['sku'] = strtoupper(Str::random(10));
    }

    // ✅ Update glavne slike (ako je poslata nova)
    if ($request->hasFile('image_main')) {
        $oldMain = $product->images()->where('is_primary', true)->first();
        if ($oldMain) {
            Storage::disk('public')->delete($oldMain->image_url);
            $oldMain->delete();
        }

        $pathMain = $request->file('image_main')->store('products', 'public');

        ProductImage::create([
            'product_id' => $product->id,
            'image_url' => $pathMain,
            'is_primary' => true,
        ]);
    }

    // ✅ Obrada galerije
    $existingImages = $request->input('existing_images', []);

    // Obriši slike koje nisu u existing_images
    $currentGallery = $product->images()->where('is_primary', false)->get();

    foreach ($currentGallery as $img) {
        if (!in_array($img->image_url, $existingImages)) {
            Storage::disk('public')->delete($img->image_url);
            $img->delete();
        }
    }

    // Dodaj nove slike
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $path = $file->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $path,
                'is_primary' => false,
            ]);
        }
    }

    // ✅ Update samog proizvoda
    $product->update([
        'name' => $validated['name'],
        'product_type' => $validated['product_type'],
        'description' => $validated['description'] ?? '',
        'category_id' => $validated['category_id'],
        'sub_category_id' => $validated['sub_category_id'],
        'base_price' => $validated['base_price'],
        'length' => $validated['length'] ?? null,
        'width' => $validated['width'] ?? null,
        'height' => $validated['height'] ?? null,
        'weight' => $validated['weight'] ?? null,
        'quantity' => $validated['quantity'] ?? 0,
        'in_stock' => $validated['in_stock'],
        'sku' => $validated['sku'] ?? $product->sku,
    ]);

    return response()->json([
        'message' => 'Product updated successfully',
        'product' => $product->load('images')
    ]);
}

    public function getAll()
    {
        $products = Product::orderBy('created_at', 'desc')->get();

        return $products;
    }

    public function delete(int $productId): bool
    {
        $product = Product::with('images')->find($productId);

        if (!$product) {
            return false;
        }

        // Obrisi slike sa diska i iz baze
        foreach ($product->images as $image) {
            if (Storage::disk('public')->exists($image->image_url)) {
                Storage::disk('public')->delete($image->image_url);
            }
            $image->delete();
        }

        // Obrisi proizvod
        return $product->delete();
    }

    public function countActive(): int
    {
        return Product::where('in_stock', 1)->count();
    }

}
