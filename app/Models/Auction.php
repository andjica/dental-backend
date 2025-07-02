<?php

namespace App\Models;

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
}
