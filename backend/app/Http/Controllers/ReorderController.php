<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ReorderController extends Controller
{
    /**
     * Legacy print_reorder.php: printable reorder sheet for low-stock items.
     */
    public function index(): View
    {
        $items = Product::query()
            ->with('category')
            ->where('stock', '<=', config('glorystock.low_stock_threshold'))
            ->orderBy('stock')
            ->get();

        return view('reports.reorder', compact('items'));
    }
}
