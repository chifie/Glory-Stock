<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'category',
        'amount',
        'description',
        'expense_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public $timestamps = false;
    public const UPDATED_AT = null;

    protected static function booted(): void
    {
        static::creating(function (Expense $expense) {
            $expense->created_at = $expense->created_at ?? now();
        });
    }

    public const CATEGORIES = [
        'Rent',
        'Electricity',
        'Water',
        'Salaries',
        'Transport',
        'Others',
    ];
}
