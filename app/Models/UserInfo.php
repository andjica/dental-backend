<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
     protected $fillable = [
        'user_id',
        'country_id',
        'city_id',
        'address',
        'zip_code',
        'phone',
        'is_finished_profile',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
   
}
