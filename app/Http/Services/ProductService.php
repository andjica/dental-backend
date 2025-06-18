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

        // Odvojimo slike ako postoje
        $images = $data['images'] ?? [];
        unset($data['images']);

        // Kreiraj proizvod
        $product = Product::create($data);

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
}