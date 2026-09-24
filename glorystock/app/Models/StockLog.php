<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    protected $table = 'stock_log';

    protected $fillable = [
        'product_id',
        'change_qty',
        'reason',
    ];

    public $timestamps = false;
    public const UPDATED_AT = null;

    protected static function booted(): void
    {
        static::creating(function (StockLog $log) {
            $log->created_at = $log->created_at ?? now();
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isIncoming(): bool
    {
        return $this->change_qty > 0;
    }
}
