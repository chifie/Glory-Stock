<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->decimal('total_price', 15);
            $table->timestamp('sale_date')->useCurrent();
            $table->string('cashier_name', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
