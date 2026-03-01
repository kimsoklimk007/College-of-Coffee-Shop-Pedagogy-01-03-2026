<?php

namespace App\Models;

use App\Models\User;
use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'name',
        'asset_category_id',
        'assigned_user_id',
        'purchase_date',
        'purchase_value',
        'depreciation_rate',
        'status',
        'unit',
        'warranty_expiry_date',
        'serial_number',
        'notes',
    ];

     public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
