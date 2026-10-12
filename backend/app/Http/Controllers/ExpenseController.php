<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    /**
     * Legacy expenses.php: expense tracker form + recent outgoings.
     */
    public function index(): View
    {
        $expenses = Expense::orderByDesc('expense_date')->orderByDesc('created_at')->get();

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Legacy expenses.php (POST): record a new expense.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'in:'.implode(',', Expense::CATEGORIES)],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:1000'],
            'expense_date' => ['required', 'date'],
        ]);

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('status', 'Expense of '.number_format((float) $validated['amount']).' recorded!');
    }
}
