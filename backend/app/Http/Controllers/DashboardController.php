<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Legacy dashboard.php: analytics + searchable/filterable inventory list.
     */
    public function index(Request $request): View
    {
        // 1. Analytics
        $totalInventoryValue = (int) Product::query()
            ->selectRaw('COALESCE(SUM(price * stock), 0) AS total')
            ->value('total');

        $todayRevenue = (int) Sale::whereDate('sale_date', today())->sum('total_price');

        $monthlyExpenses = (int) Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $monthlyRevenue = (int) Sale::whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->sum('total_price');

        $netProfit = $monthlyRevenue - $monthlyExpenses;

        // 2. Product query with filters
        $products = Product::query()
            ->with('category')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search')->trim().'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('sku', 'like', $term));
            })
            ->when($request->integer('category_id') > 0, fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->boolean('low_stock'), fn ($query) => $query->where('stock', '<=', config('glorystock.low_stock_threshold')))
            ->orderBy('stock')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('dashboard.index', compact(
            'totalInventoryValue', 'todayRevenue', 'monthlyExpenses',
            'monthlyRevenue', 'netProfit', 'products', 'categories'
        ));
    }
}
