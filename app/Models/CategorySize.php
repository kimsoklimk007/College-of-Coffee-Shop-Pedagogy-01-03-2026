<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorySize extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'size',
        'price_khr',
        'price_usd',
        'is_active'
    ];

    protected $casts = [
        'price_khr' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
