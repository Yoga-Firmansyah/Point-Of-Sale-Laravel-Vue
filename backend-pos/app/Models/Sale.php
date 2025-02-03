<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_item',
        'total_price',
        'discount',
        'grand_total',
        'pay',
        'change',
    ];

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class)->with('product');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
