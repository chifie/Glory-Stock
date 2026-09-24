<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    /**
     * Legacy pos.php (GET): show the checkout form and recent sales.
     */
    public function index(): View
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get(['id', 'name', 'price', 'stock']);

        $recentSales = Sale::query()
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->select('sales.*', 'products.name as product_name')
            ->orderByDesc('sales.sale_date')
            ->limit(5)
            ->get();

        return view('pos.index', compact('products', 'recentSales'));
    }

    /**
     * Legacy pos.php (POST): process a sale transactionally with stock audit logging.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = (int) $validated['quantity'];

        if ($qty > $product->stock) {
            return back()->withErrors([
                'quantity' => "⚠️ Stock Alert: Only {$product->stock} available.",
            ])->withInput();
        }

        $sale = DB::transaction(function () use ($product, $qty) {
            // 1. Record the sale
            $sale = Sale::create([
                'product_id' => $product->id,
                'quantity' => $qty,
                'total_price' => $product->price * $qty,
                'sale_date' => now(),
                'cashier_name' => auth()->user()->username,
            ]);

            // 2. Update product stock
            $product->decrement('stock', $qty);

            // 3. Audit log
            StockLog::create([
                'product_id' => $product->id,
                'change_qty' => -$qty,
                'reason' => 'Sale (Staff: '.auth()->user()->username.')',
            ]);

            return $sale;
        });

        return redirect()
            ->route('pos.index', ['success' => 1, 'amount' => $sale->total_price, 'receipt' => $sale->id])
            ->with('status', 'Transaction complete!');
    }
}
