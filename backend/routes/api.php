<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DataController;
use Illuminate\Support\Facades\Route;

// --- Public ---
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// --- Any authenticated user ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/pos', [DataController::class, 'posIndex']);
    Route::post('/pos', [DataController::class, 'posStore']);

    Route::get('/dashboard', [DataController::class, 'dashboardIndex']);

    Route::get('/sales-history', [DataController::class, 'salesHistory']);
    Route::get('/stock-log', [DataController::class, 'stockLogIndex']);
    Route::get('/receipt/{sale}', [DataController::class, 'receiptShow']);

    Route::get('/daily-close', [DataController::class, 'dailyClose']);
    Route::get('/reorder', [DataController::class, 'reorder']);

    // --- Admin only (legacy EnsureUserIsAdmin middleware) ---
    Route::middleware('admin')->group(function () {
        Route::delete('/sales/{sale}', [DataController::class, 'voidSale']);

        Route::get('/reports', [DataController::class, 'reportsIndex']);

        Route::post('/products', [DataController::class, 'productStore']);
        Route::put('/products/{product}', [DataController::class, 'productUpdate']);
        Route::delete('/products/{product}', [DataController::class, 'productDestroy']);

        Route::get('/categories', [DataController::class, 'categoriesIndex']);
        Route::post('/categories', [DataController::class, 'categoryStore']);

        Route::get('/expenses', [DataController::class, 'expensesIndex']);
        Route::post('/expenses', [DataController::class, 'expenseStore']);

        Route::get('/users', [DataController::class, 'usersIndex']);
        Route::post('/users', [DataController::class, 'userStore']);
        Route::delete('/users/{user}', [DataController::class, 'userDestroy']);
    });
});
