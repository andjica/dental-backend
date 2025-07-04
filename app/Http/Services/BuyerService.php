<?php
namespace App\Http\Services;

use App\Models\User;
use App\Http\Interfaces\BuyerInterface;

class BuyerService implements BuyerInterface
{
    public function countTotal(): int
    {
        return User::where('role_id', 4)->count();
    }
}
