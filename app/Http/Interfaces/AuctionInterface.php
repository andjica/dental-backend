<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Auction;

interface AuctionInterface
{
    public function getAll(): Collection;
    public function getAllByUserId(int $userId): ?Collection;
    public function create(array $data): Auction;
    public function view(int $id): ?Auction;
    public function delete(int $id): bool;
    public function update(array $data, int $auctionId): ?Auction;

}
