<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    /**
     * Legacy receipt.php: printable receipt for a single sale.
     */
    public function show(Sale $sale): View
    {
        $sale->load('product');

        return view('receipt.show', compact('sale'));
    }
}
