<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'owner_id',
        'address',
        'phone',
        'email',
        'status',
        'description',
    ];

    /**
     * Get the owner (admin) of the shop.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all users belonging to this shop.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all orders for this shop through users.
     */
    public function orders()
    {
        return $this->hasManyThrough(Order::class, User::class);
    }
}
