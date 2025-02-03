<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'total_item',
        'total_price',
        'discount',
        'grand_total',
    ];

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class)->with('product');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
