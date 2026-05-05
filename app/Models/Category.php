<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'khmer_name', 'start_date', 'end_date'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function sizes()
    {
        return $this->hasMany(CategorySize::class);
    }
}
