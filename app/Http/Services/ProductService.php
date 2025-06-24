<?php

namespace App\Http\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Interfaces\ProductInterface;

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



    public function update(array $data, int $productId)
    {
        $product = Product::findOrFail($productId);

        if(!$product)
        {
            return null;
        }

        $data['base_price'] = number_format((float) $data['base_price'], 2, '.', '');
        $data['in_stock'] = ($data['in_stock'] == 1) ? 1 : 0;

        if (empty($data['sku'])) {
            $data['sku'] = strtoupper(Str::random(10));
        }

        
        $images = $data['images'] ?? [];
        unset($data['images']);

      
        if (isset($data['main_image'])) {
            $oldMain = $product->images()->where('is_primary', true)->first();

            if ($oldMain) {
                Storage::disk('public')->delete($oldMain->image_url);
                $oldMain->delete();
            }

            $mainImage = $data['main_image'];
            $pathMain = $mainImage->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $pathMain,
                'is_primary' => true,
            ]);
        }

      
        if (!empty($images)) {
            $oldExtraImages = $product->images()->where('is_primary', false)->get();

            foreach ($oldExtraImages as $img) {
                Storage::disk('public')->delete($img->image_url);
                $img->delete();
            }

            foreach ($images as $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $path,
                    'is_primary' => false,
                ]);
            }
        }

     
        $product->update($data);

        return $product->load('images');
    }
}
