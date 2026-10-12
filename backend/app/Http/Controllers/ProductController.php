<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Legacy add_product.php (GET): show the create form.
     */
    public function create(): View
    {
        return view('products.form', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
            'route' => route('products.store'),
            'method' => 'POST',
        ]);
    }

    /**
     * Legacy add_product.php / save_product.php (POST): validate and store a product.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        Product::create($validated);

        return redirect()->route('dashboard')->with('status', 'Product added successfully!');
    }

    /**
     * Legacy edit_product.php (GET): show the edit form.
     */
    public function edit(Product $product): View
    {
        return view('products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'route' => route('products.update', $product),
            'method' => 'PUT',
        ]);
    }

    /**
     * Legacy edit_product.php (POST) / update_product.php: update a product.
     * Stock changes are recorded in the audit trail (legacy behaviour).
     */
    public function update(Request $request, Product $product): RedirectResponse
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

        return redirect()->route('dashboard')->with('status', 'Product updated successfully!');
    }

    /**
     * Legacy delete_product.php: delete a product unless it has sales history.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->sales()->exists()) {
            return redirect()->route('dashboard')->with('error', 'Product has sales records and cannot be deleted.');
        }

        $product->delete();

        return redirect()->route('dashboard')->with('status', 'Product deleted successfully!');
    }
}
