<?php

namespace App\Http\Controllers;

use App\Models\StockLog;
use Illuminate\View\View;

class StockLogController extends Controller
{
    /**
     * Legacy stock_log.php: full stock movement audit trail.
     */
    public function index(): View
    {
        $logs = StockLog::query()
            ->join('products', 'stock_log.product_id', '=', 'products.id')
            ->select('stock_log.*', 'products.name as product_name', 'products.sku')
            ->orderByDesc('stock_log.created_at')
            ->get();

        return view('stock-log.index', compact('logs'));
    }
}
