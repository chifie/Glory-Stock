<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\View\View;

class DailyCloseController extends Controller
{
    /**
     * Legacy daily_close.php: end-of-day settlement report.
     */
    public function index(): View
    {
        $today = now()->format('Y-m-d');

        $summary = [
            'total_transactions' => Sale::whereDate('sale_date', $today)->count(),
            'total_cash' => (int) Sale::whereDate('sale_date', $today)->sum('total_price'),
            'total_items' => (int) Sale::whereDate('sale_date', $today)->sum('quantity'),
        ];

        $dailySales = Sale::query()
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->select('sales.*', 'products.name as product_name')
            ->whereDate('sale_date', $today)
            ->orderByDesc('sale_date')
            ->get();

        return view('reports.daily-close', compact('summary', 'dailySales', 'today'));
    }
}
