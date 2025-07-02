<?php

namespace App\Services;

use App\Interfaces\AuctionInterface;
use App\Models\Auction;

class AuctionService implements AuctionInterface
{
    public function create(array $data): Auction
    {
        return Auction::create($data);
    }

    public function view(int $id): ?Auction
    {
        return Auction::find($id);
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
