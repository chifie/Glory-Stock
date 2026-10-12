<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * JSON API consumed by the React SPA. Mirrors the behaviour of the
 * legacy web controllers (identical aggregation and validation logic)
 * but returns JSON instead of Blade views.
 */
class DataController extends Controller
{
    // ---------------- POS ----------------

    public function posIndex(): JsonResponse
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get(['id', 'name', 'price', 'stock']);

        $recentSales = Sale::query()
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->select('sales.*', 'products.name as product_name')
            ->orderByDesc('sales.sale_date')
            ->limit(5)
            ->get(['sales.id', 'product_name', 'quantity', 'total_price', 'sale_date']);

        return response()->json(['products' => $products, 'recentSales' => $recentSales]);
    }

    public function posStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = (int) $validated['quantity'];

        if ($qty > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Stock Alert: Only {$product->stock} available.",
            ]);
        }

        $sale = DB::transaction(function () use ($product, $qty, $request) {
            $sale = Sale::create([
                'product_id' => $product->id,
                'quantity' => $qty,
                'total_price' => $product->price * $qty,
                'sale_date' => now(),
                'cashier_name' => $request->user()->username,
            ]);

            $product->decrement('stock', $qty);

            StockLog::create([
                'product_id' => $product->id,
                'change_qty' => -$qty,
                'reason' => 'Sale (Staff: '.$request->user()->username.')',
            ]);

            return $sale;
        });

        return response()->json([
            'message' => 'Transaction complete!',
            'receipt_id' => $sale->id,
            'amount' => (float) $sale->total_price,
        ], 201);
    }

    // ---------------- Dashboard ----------------

    public function dashboardIndex(Request $request): JsonResponse
    {
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

        $products = Product::query()
            ->with('category:id,name')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search')->trim().'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('sku', 'like', $term));
            })
            ->when($request->integer('category_id') > 0, fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->boolean('low_stock'), fn ($query) => $query->where('stock', '<=', config('glorystock.low_stock_threshold')))
            ->orderBy('stock')
            ->get();

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'stats' => [
                'totalInventoryValue' => $totalInventoryValue,
                'todayRevenue' => $todayRevenue,
                'monthlyExpenses' => $monthlyExpenses,
                'monthlyRevenue' => $monthlyRevenue,
                'netProfit' => $netProfit,
            ],
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    // ---------------- Sales history + void ----------------

    public function salesHistory(Request $request): JsonResponse
    {
        $startDate = $request->date('start_date', 'Y-m-d') ?? now()->subDays(30)->startOfDay();
        $endDate = $request->date('end_date', 'Y-m-d') ?? now()->endOfDay();

        $sales = Sale::query()
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->select('sales.id', 'products.name as product_name', 'products.sku', 'sales.quantity', 'sales.total_price', 'sales.sale_date', 'sales.cashier_name')
            ->whereBetween('sales.sale_date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderByDesc('sales.sale_date')
            ->get();

        $topProduct = Sale::query()
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(sales.quantity) AS total_qty')
            ->whereBetween('sales.sale_date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('products.name')
            ->orderByDesc('total_qty')
            ->first();

        return response()->json([
            'sales' => $sales,
            'totalRevenue' => (int) $sales->sum('total_price'),
            'topProduct' => $topProduct ? ['name' => $topProduct->name, 'total_qty' => (int) $topProduct->total_qty] : null,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }

    public function voidSale(Sale $sale): JsonResponse
    {
        DB::transaction(function () use ($sale) {
            $sale->product->increment('stock', $sale->quantity);

            $sale->product->stockLogs()->create([
                'change_qty' => $sale->quantity,
                'reason' => 'Sale Voided (Sale #'.$sale->id.')',
            ]);

            $sale->delete();
        });

        return response()->json(['message' => 'Sale voided successfully!']);
    }

    // ---------------- Stock audit trail ----------------

    public function stockLogIndex(): JsonResponse
    {
        $logs = StockLog::query()
            ->join('products', 'stock_log.product_id', '=', 'products.id')
            ->select('stock_log.id', 'products.name as product_name', 'products.sku', 'stock_log.change_qty', 'stock_log.reason', 'stock_log.created_at')
            ->orderByDesc('stock_log.created_at')
            ->get();

        return response()->json(['logs' => $logs]);
    }

    // ---------------- Receipt ----------------

    public function receiptShow(Sale $sale): JsonResponse
    {
        $sale->load('product:id,name');

        return response()->json([
            'sale' => [
                'id' => $sale->id,
                'product_name' => $sale->product->name,
                'quantity' => (int) $sale->quantity,
                'total_price' => (float) $sale->total_price,
                'sale_date' => $sale->sale_date->toISOString(),
                'cashier_name' => $sale->cashier_name,
            ],
            'store' => [
                'phone' => config('glorystock.store_phone'),
                'city' => config('glorystock.store_city'),
            ],
        ]);
    }

    // ---------------- Reports (admin) ----------------

    public function reportsIndex(): JsonResponse
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
            ->slice(-6)
            ->values();

        $topProducts = Sale::query()
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(sales.quantity) AS total_sold')
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(6)
            ->get();

        return response()->json([
            'months' => $months->all(),
            'revenueValues' => $months->map(fn ($m) => $salesByMonth[$m] ?? 0)->values()->all(),
            'expenseValues' => $months->map(fn ($m) => $expensesByMonth[$m] ?? 0)->values()->all(),
            'topProducts' => $topProducts,
        ]);
    }

    // ---------------- Daily close ----------------

    public function dailyClose(): JsonResponse
    {
        $today = now()->format('Y-m-d');

        $dailySales = Sale::query()
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->select('sales.sale_date', 'products.name as product_name', 'sales.quantity', 'sales.total_price')
            ->whereDate('sale_date', $today)
            ->orderByDesc('sale_date')
            ->get();

        return response()->json([
            'today' => $today,
            'summary' => [
                'total_transactions' => count($dailySales),
                'total_cash' => (int) $dailySales->sum('total_price'),
                'total_items' => (int) $dailySales->sum('quantity'),
            ],
            'dailySales' => $dailySales,
        ]);
    }

    // ---------------- Reorder sheet ----------------

    public function reorder(): JsonResponse
    {
        $items = Product::query()
            ->with('category:id,name')
            ->where('stock', '<=', config('glorystock.low_stock_threshold'))
            ->orderBy('stock')
            ->get();

        return response()->json(['items' => $items, 'today' => now()->format('D, d M Y')]);
    }

    // ---------------- Products (admin) ----------------

    public function productStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $product = Product::create($validated);

        return response()->json(['message' => 'Product added successfully!', 'product' => $product], 201);
    }

    public function productUpdate(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,'.$product->id],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($product, $validated) {
            $oldStock = $product->stock;

            $product->update($validated);

            $difference = $product->stock - $oldStock;

            if ($difference !== 0) {
                StockLog::create([
                    'product_id' => $product->id,
                    'change_qty' => $difference,
                    'reason' => $difference > 0 ? 'Manual Restock' : 'Manual Reduction/Correction',
                ]);
            }
        });

        return response()->json(['message' => 'Product updated successfully!', 'product' => $product->fresh()]);
    }

    public function productDestroy(Product $product): JsonResponse
    {
        if ($product->sales()->exists()) {
            throw ValidationException::withMessages([
                'product' => 'Product has sales records and cannot be deleted.',
            ]);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully!']);
    }

    // ---------------- Categories (admin) ----------------

    public function categoriesIndex(): JsonResponse
    {
        return response()->json([
            'categories' => Category::withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function categoryStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => "Category '{$validated['name']}' added successfully!",
            'category' => $category,
        ], 201);
    }

    // ---------------- Expenses (admin) ----------------

    public function expensesIndex(): JsonResponse
    {
        return response()->json([
            'expenses' => Expense::orderByDesc('expense_date')->orderByDesc('created_at')->get(),
            'categories' => Expense::CATEGORIES,
        ]);
    }

    public function expenseStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'in:'.implode(',', Expense::CATEGORIES)],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:1000'],
            'expense_date' => ['required', 'date'],
        ]);

        $expense = Expense::create($validated);

        return response()->json([
            'message' => 'Expense of '.number_format((float) $validated['amount']).' recorded!',
            'expense' => $expense,
        ], 201);
    }

    // ---------------- Users (admin) ----------------

    public function usersIndex(): JsonResponse
    {
        return response()->json([
            'users' => User::orderByDesc('id')->get(['id', 'username', 'role']),
        ]);
    }

    public function userStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', Password::min(6)],
            'role' => ['required', 'in:admin,staff'],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => "Account for '{$validated['username']}' created successfully!",
            'user' => ['id' => $user->id, 'username' => $user->username, 'role' => $user->role],
        ], 201);
    }

    public function userDestroy(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot delete your own account.',
            ]);
        }

        $user->delete();

        return response()->json(['message' => 'User removed successfully!']);
    }
}
