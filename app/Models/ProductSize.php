<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    //
    protected $fillable = ['product_id','size','price','price_khr','price_usd','currency'];

    protected $casts = [
        'price_khr' => 'decimal:2',
        'price_usd' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
