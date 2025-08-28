<?php

namespace App\Models;

use App\Models\Bid;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
     protected $fillable = [
        'user_id',
        'name',
        'description',
        'base_price',
        'auction_date',
    ];

    //user who created auction
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function images()
    {
        return $this->hasMany(AuctionImage::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
}
