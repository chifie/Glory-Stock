<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the database with the legacy GloryStock data.
     *
     * Existing bcrypt password hashes are preserved verbatim so current
     * users (fetty, monica, chifie) can log in with their old passwords.
     */
    public function run(): void
    {
        // --- Users (legacy hashes preserved) ---
        User::updateOrCreate(['username' => 'fetty'], [
            'password' => '$2y$10$Md4cpBU04VoKmDABqpUNGeB.ReO905h97MrXOf9Agb0A6snhPXU.6',
            'role' => 'staff',
        ]);
        User::updateOrCreate(['username' => 'monica'], [
            'password' => '$2y$10$y4aQ09wE5NEbeObpoZ9FPuSPlCRm1pMxOwx8CwpOb2mIMEBB7VTri',
            'role' => 'admin',
        ]);
        User::updateOrCreate(['username' => 'chifie'], [
            'password' => '$2y$10$FCDf0/IAwFaqZrZECT8ZR.oLNYKtDNdKHBkA.5OmelJh0hJEltsyq',
            'role' => 'staff',
        ]);

        // --- Categories ---
        $food = Category::create(['name' => 'Food']);
        $drinks = Category::create(['name' => 'Drinks']);
        $electronics = Category::create(['name' => 'Electronics']);
        Category::create(['name' => 'Toiletries']);

        // --- Products ---
        $coke = Product::create(['name' => 'Coca Cola 500ml', 'sku' => 'COKE-001', 'price' => 5000, 'stock' => 17, 'category_id' => $drinks->id]);
        $flour = Product::create(['name' => 'Azam Wheat Flour 2kg', 'sku' => 'AZM-WHT-02', 'price' => 80000, 'stock' => 7, 'category_id' => $food->id]);
        $rice = Product::create(['name' => 'mchele', 'sku' => 'FRG_003', 'price' => 30000, 'stock' => 6, 'category_id' => $food->id]);
        $iphone = Product::create(['name' => 'iphone17', 'sku' => 'IPH_006', 'price' => 150000, 'stock' => 19, 'category_id' => $electronics->id]);
        $redblue = Product::create(['name' => 'redblue', 'sku' => 'RW_009', 'price' => 4000, 'stock' => 4, 'category_id' => $drinks->id]);

        // --- Sales (legacy rows) ---
        Sale::create(['product_id' => $flour->id, 'quantity' => 1, 'total_price' => 80000, 'sale_date' => '2026-03-07 08:14:40', 'cashier_name' => null]);
        Sale::create(['product_id' => $coke->id, 'quantity' => 1, 'total_price' => 5000, 'sale_date' => '2026-03-07 08:39:06', 'cashier_name' => null]);
        Sale::create(['product_id' => $flour->id, 'quantity' => 1, 'total_price' => 80000, 'sale_date' => '2026-03-14 14:06:52', 'cashier_name' => null]);
        Sale::create(['product_id' => $rice->id, 'quantity' => 1, 'total_price' => 30000, 'sale_date' => '2026-03-14 14:10:16', 'cashier_name' => null]);
        Sale::create(['product_id' => $coke->id, 'quantity' => 1, 'total_price' => 5000, 'sale_date' => '2026-03-15 08:44:21', 'cashier_name' => null]);
        Sale::create(['product_id' => $coke->id, 'quantity' => 1, 'total_price' => 5000, 'sale_date' => '2026-03-15 08:45:44', 'cashier_name' => null]);
        Sale::create(['product_id' => $coke->id, 'quantity' => 1, 'total_price' => 5000, 'sale_date' => '2026-03-15 08:45:57', 'cashier_name' => null]);
        Sale::create(['product_id' => $iphone->id, 'quantity' => 1, 'total_price' => 150000, 'sale_date' => '2026-03-15 10:35:55', 'cashier_name' => null]);
        Sale::create(['product_id' => $iphone->id, 'quantity' => 1, 'total_price' => 150000, 'sale_date' => '2026-03-20 19:24:20', 'cashier_name' => null]);
        Sale::create(['product_id' => $redblue->id, 'quantity' => 1, 'total_price' => 4000, 'sale_date' => '2026-03-20 19:40:59', 'cashier_name' => null]);

        // --- Stock log (legacy audit trail) ---
        StockLog::create(['product_id' => $rice->id, 'change_qty' => 1, 'reason' => 'Manual Restock', 'created_at' => '2026-03-15 08:57:29']);
        StockLog::create(['product_id' => $iphone->id, 'change_qty' => -1, 'reason' => 'Sale (Staff: Levina)', 'created_at' => '2026-03-15 10:35:55']);
        StockLog::create(['product_id' => $iphone->id, 'change_qty' => -1, 'reason' => 'Sale (Staff: monica)', 'created_at' => '2026-03-20 19:24:20']);
        StockLog::create(['product_id' => $redblue->id, 'change_qty' => -1, 'reason' => 'Sale (Staff: chifie)', 'created_at' => '2026-03-20 19:40:59']);

        // --- Expenses ---
        Expense::create(['category' => 'Water', 'amount' => 10000, 'description' => 'water bills', 'expense_date' => '2026-03-15', 'created_at' => '2026-03-15 08:47:28']);
        Expense::create(['category' => 'Electricity', 'amount' => 20000, 'description' => 'electricity', 'expense_date' => '2026-03-20', 'created_at' => '2026-03-20 19:28:29']);
    }
}
