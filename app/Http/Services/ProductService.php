<?php
namespace App\Http\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Auth;
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

        
        if($data['in_stock'] == 1)
        {
            $data['in_stock'] = 1;
        }
        else
        {
            $data['in_stock'] = 0;
        }

        

        // Odvojimo slike ako postoje
        $images = $data['images'] ?? [];
        unset($data['images']);

        // Kreiraj proizvod
        $product = Product::create($data);
        return $product;
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

        if(!$product)
        {
            return null;
        }

        return $product;
    }

    public function getByUserId(int $userId)
    {
        $products = Product::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        return $products;
    }
}