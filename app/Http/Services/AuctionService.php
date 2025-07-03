<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Auction;
use App\Models\AuctionImage;
use Illuminate\Support\Facades\Storage;
use App\Http\Interfaces\AuctionInterface;
use Illuminate\Database\Eloquent\Collection;

class AuctionService implements AuctionInterface
{
    public function getAll(): Collection
    {

        return Auction::orderBy('created_at', 'desc')->get();
    }

    public function getAllByUserId(int $userId): ?Collection
    {
         $user = User::find($userId);

        if(!$user) 
        {
            return  null;
        }

        $auctions = Auction::where('user_id', $userId)
        ->orderBy('created_at', 'desc')->get();
        
        return $auctions;
    }

    public function create(array $data): Auction
    {

        $auction = Auction::create([
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'base_price' => number_format((float) $data['base_price'], 2, '.', ''),
            'auction_date' => $data['auction_date'],
        ]);


        if (isset($data['image_main']) && $data['image_main']->isValid()) {
            $pathMain = $data['image_main']->store('auctions', 'public');

            AuctionImage::create([
                'auction_id' => $auction->id,
                'image_url' => $pathMain,
                'is_primary' => true,
            ]);
        }


        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $img) {
                if ($img->isValid()) {
                    $path = $img->store('auctions', 'public');

                    AuctionImage::create([
                        'auction_id' => $auction->id,
                        'image_url' => $path,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        return $auction->load('images');
    }

    public function view(int $id): ?Auction
    {
        return Auction::find($id);
    }

    public function update(array $data, int $auctionId): ?Auction
    {
        $auction = Auction::with('images')->find($auctionId);

        if (!$auction) {
            return null;
        }

    
        $auction->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'base_price' => number_format((float) $data['base_price'], 2, '.', ''),
            'auction_date' => $data['auction_date'],
        ]);

       
        $existingImages = $data['existing_images'] ?? [];

       
        foreach ($auction->images as $img) {
            if (!in_array($img->image_url, $existingImages)) {
                Storage::disk('public')->delete($img->image_url);
                $img->delete();
            }
        }

       
        if (isset($data['image_main']) && $data['image_main']->isValid()) {
            // Obriši staru primarnu
            $oldMain = $auction->images()->where('is_primary', true)->first();
            if ($oldMain) {
                Storage::disk('public')->delete($oldMain->image_url);
                $oldMain->delete();
            }

          
            $pathMain = $data['image_main']->store('auctions', 'public');

            AuctionImage::create([
                'auction_id' => $auction->id,
                'image_url' => $pathMain,
                'is_primary' => true,
            ]);
        }

      
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $file) {
                if ($file->isValid()) {
                    $path = $file->store('auctions', 'public');

                    AuctionImage::create([
                        'auction_id' => $auction->id,
                        'image_url' => $path,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        return $auction->load('images');
    }

    public function delete(int $id): bool
    {
        $auction = Auction::find($id);
        if ($auction) {
            return $auction->delete();
        }
        return false;
    }
}
