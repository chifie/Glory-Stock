<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'total_price',
        'sale_date',
        'cashier_name',
    ];

    protected $casts = [
        'total_price' => 'decimal:0',
        'sale_date' => 'datetime',
    ];

    public $timestamps = false;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
