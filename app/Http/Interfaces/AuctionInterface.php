<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Auction;

interface AuctionInterface
{
    public function create(array $data): Auction;
    public function view(int $id): ?Auction;
    public function delete(int $id): bool;
}
