<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesHistoryController extends Controller
{
    /**
     * Legacy sales_history.php: date-filtered sales with revenue stats.
     */
    public function index(Request $request): View
    {
        $startDate = $request->date('start_date', 'Y-m-d') ?? now()->subDays(30)->startOfDay();
        $endDate = $request->date('end_date', 'Y-m-d') ?? now()->endOfDay();

        $sales = Sale::query()
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->select('sales.*', 'products.name as product_name', 'products.sku')
            ->whereBetween('sales.sale_date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderByDesc('sales.sale_date')
            ->get();

        $totalRevenue = (int) $sales->sum('total_price');

        $topProduct = Sale::query()
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(sales.quantity) AS total_qty')
            ->whereBetween('sales.sale_date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('products.name')
            ->orderByDesc('total_qty')
            ->first();

        return view('sales.history', compact(
            'sales', 'totalRevenue', 'topProduct', 'startDate', 'endDate'
        ));
    }

    /**
     * Legacy void_sale.php: admin voids a sale, restoring stock.
     */
    public function void(Sale $sale)
    {
        // Restore stock
        $sale->product->increment('stock', $sale->quantity);

        // Log the restock in the audit trail
        $sale->product->stockLogs()->create([
            'change_qty' => $sale->quantity,
            'reason' => 'Sale Voided (Sale #'.$sale->id.')',
        ]);

        $sale->delete();

        return redirect()->route('sales.history')->with('status', 'Sale voided successfully!');
    }
}
