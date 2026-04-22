<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'name_kh', 'qty', 'category_id', 'description', 'image', 'price_khr', 'price_usd'];

    protected $casts = [
        'price_khr' => 'decimal:2',
        'price_usd' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }
}
