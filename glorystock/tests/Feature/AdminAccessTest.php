<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_access_admin_pages(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->get(route('users.index'))->assertRedirect(route('access.denied'));
        $this->actingAs($staff)->get(route('reports'))->assertRedirect(route('access.denied'));
        $this->actingAs($staff)->get(route('expenses.index'))->assertRedirect(route('access.denied'));
        $this->actingAs($staff)->get(route('products.create'))->assertRedirect(route('access.denied'));
    }

    public function test_staff_cannot_delete_products(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $product = Product::factory()->create();

        $this->actingAs($staff)
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('access.denied'));

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_admin_can_delete_product_without_sales(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $this->actingAs($admin)
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_cannot_delete_product_with_sales(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        Sale::create([
            'product_id' => $product->id,
            'quantity' => 1,
            'total_price' => $product->price,
            'sale_date' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_product_update_logs_stock_change_to_audit_trail(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($admin)
            ->put(route('products.update', $product), [
                'name' => $product->name,
                'sku' => $product->sku,
                'category_id' => $product->category_id,
                'price' => $product->price,
                'stock' => 25,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertSame(25, $product->fresh()->stock);

        $this->assertDatabaseHas('stock_log', [
            'product_id' => $product->id,
            'change_qty' => 15,
            'reason' => 'Manual Restock',
        ]);
    }

    public function test_admin_can_void_a_sale_and_restore_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['stock' => 5]);
        $sale = Sale::create([
            'product_id' => $product->id,
            'quantity' => 2,
            'total_price' => $product->price * 2,
            'sale_date' => now(),
            'cashier_name' => 'someone',
        ]);

        $this->actingAs($admin)
            ->delete(route('sales.void', $sale))
            ->assertRedirect(route('sales.history'));

        $this->assertSame(7, $product->fresh()->stock);
        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
        $this->assertDatabaseHas('stock_log', [
            'product_id' => $product->id,
            'change_qty' => 2,
        ]);
    }
}
