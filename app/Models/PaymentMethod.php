<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mollie_customer_id',
        'mollie_mandate_id',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
