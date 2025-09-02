<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'postal_code',
        'phone_code',
        'logo',
        'name',
        'tax_number',         
        'registration_number',
        'email',
        'country_id',
        'city_id',
        'is_finished_profile',
        'active'
    ];

    protected $casts = [
        'is_finished_profile' => 'boolean',
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
