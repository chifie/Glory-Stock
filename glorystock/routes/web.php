<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DailyCloseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReorderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesHistoryController;
use App\Http\Controllers\StockLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// --- Guest (legacy login.php / register.php) ---
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store')->middleware('throttle:5,1');
});

// --- Authenticated (any role) ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/access-denied', [AuthController::class, 'accessDenied'])->name('access.denied');

    // POS terminal (legacy pos.php)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');

    // Inventory dashboard (legacy dashboard.php)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Sales history (legacy sales_history.php) + void (legacy void_sale.php)
    Route::get('/sales-history', [SalesHistoryController::class, 'index'])->name('sales.history');
    Route::delete('/sales/{sale}', [SalesHistoryController::class, 'void'])->name('sales.void')->middleware('admin');

    // Stock audit trail (legacy stock_log.php)
    Route::get('/stock-log', [StockLogController::class, 'index'])->name('stock.log');

    // Receipt (legacy receipt.php)
    Route::get('/receipt/{sale}', [ReceiptController::class, 'show'])->name('receipt.show');

    // Reports (legacy daily_close.php / print_reorder.php) - any logged-in user
    Route::get('/daily-close', [DailyCloseController::class, 'index'])->name('daily.close');
    Route::get('/reorder', [ReorderController::class, 'index'])->name('reorder');

    // --- Admin only ---
    Route::middleware('admin')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');

        // Products (legacy add_product.php, edit_product.php, delete_product.php)
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Categories (legacy categories.php)
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

        // Expenses (legacy expenses.php)
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

        // Staff management (legacy users.php / delete_user.php)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
