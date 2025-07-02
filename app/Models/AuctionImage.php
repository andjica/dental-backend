<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuctionImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'image_url',
        'is_primary',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }
}
