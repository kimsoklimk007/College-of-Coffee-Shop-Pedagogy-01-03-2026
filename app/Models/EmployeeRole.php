<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_kh',
        'description',
        'description_kh',
        'permissions',
        'base_salary',
        'status',
        'is_active',
    ];

    protected $attributes = [
        'status' => 'Active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'base_salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class, 'role_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function getDisplayNameAttribute()
    {
        return app()->getLocale() === 'km' && $this->name_kh ? $this->name_kh : $this->name;
    }

    public function getDisplayDescriptionAttribute()
    {
        return app()->getLocale() === 'km' && $this->description_kh ? $this->description_kh : $this->description;
    }
}
