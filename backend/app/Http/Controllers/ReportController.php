<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Legacy reports.php: monthly revenue vs expenses + best sellers.
     * Grouping is done in PHP so it works on MySQL and SQLite alike.
     */
    public function index(): View
    {
        $salesByMonth = Sale::get(['sale_date', 'total_price'])
            ->groupBy(fn ($sale) => $sale->sale_date->format('M Y'))
            ->map(fn ($group) => (float) $group->sum('total_price'));

        $expensesByMonth = Expense::get(['expense_date', 'amount'])
            ->groupBy(fn ($expense) => $expense->expense_date->format('M Y'))
            ->map(fn ($group) => (float) $group->sum('amount'));

        $months = $salesByMonth->keys()
            ->merge($expensesByMonth->keys())
            ->unique()
            ->sortBy(fn ($month) => Carbon::createFromFormat('M Y', $month)->timestamp)
            ->values()
            ->slice(-6) // most recent 6 months
            ->values();

        $revenueValues = $months->map(fn ($m) => $salesByMonth[$m] ?? 0);
        $expenseValues = $months->map(fn ($m) => $expensesByMonth[$m] ?? 0);

        $topProducts = Sale::query()
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(sales.quantity) AS total_sold')
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(6)
            ->get();

        return view('reports.index', compact(
            'months', 'revenueValues', 'expenseValues', 'topProducts'
        ));
    }
}
