<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'category',
        'amount',
        'currency',
        'exchange_rate',
        'amount_khr',
        'description',
        'transaction_date',
        'user_id',
        'order_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:2',
        'amount_khr' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public static function getIncomeCategories()
    {
        return [
            'product_sales' => 'Product Sales',
            'delivery_fee' => 'Delivery Fee',
            'other_income' => 'Other Income'
        ];
    }

    public static function getExpenseCategories()
    {
        return [
            'purchase' => 'Purchase (Ingredients)',
            'salary' => 'Salary',
            'rent' => 'Rent',
            'utilities' => 'Utilities',
            'supplies' => 'Supplies',
            'maintenance' => 'Maintenance',
            'marketing' => 'Marketing',
            'other_expense' => 'Other Expense'
        ];
    }
}
