<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_and_user(): void
    {
        User::factory()->create(['username' => 'monica', 'password' => 'secret123', 'role' => 'admin']);

        $response = $this->postJson('/api/login', [
            'username' => 'monica',
            'password' => 'secret123',
            'role' => 'admin',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'username', 'role']]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_login_fails_when_role_does_not_match(): void
    {
        User::factory()->create(['username' => 'fetty', 'password' => 'secret123', 'role' => 'staff']);

        $this->postJson('/api/login', [
            'username' => 'fetty',
            'password' => 'secret123',
            'role' => 'admin',
        ])->assertJsonValidationErrors('role');
    }

    public function test_admin_registration_requires_security_key(): void
    {
        $this->postJson('/api/register', [
            'username' => 'newadmin',
            'password' => 'secret123',
            'role' => 'admin',
            'admin_key' => 'wrong-key',
        ])->assertJsonValidationErrors('admin_key');

        $this->postJson('/api/register', [
            'username' => 'newadmin',
            'password' => 'secret123',
            'role' => 'admin',
            'admin_key' => config('glorystock.admin_key'),
        ])->assertCreated();
    }

    public function test_cashier_can_process_a_sale_via_api(): void
    {
        $cashier = User::factory()->create(['role' => 'staff']);
        $category = Category::create(['name' => 'Drinks']);
        $product = Product::create(['name' => 'Coke', 'sku' => 'COKE-1', 'price' => 5000, 'stock' => 10, 'category_id' => $category->id]);

        $response = $this->actingAs($cashier, 'sanctum')
            ->postJson('/api/pos', ['product_id' => $product->id, 'quantity' => 2]);

        $response->assertCreated()
            ->assertJsonPath('amount', 10000)
            ->assertJsonPath('receipt_id', $product->sales()->first()->id);

        $this->assertDatabaseHas('sales', ['product_id' => $product->id, 'quantity' => 2]);
        $this->assertDatabaseHas('stock_log', ['product_id' => $product->id, 'change_qty' => -2]);
        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_sale_is_rejected_when_stock_is_insufficient(): void
    {
        $cashier = User::factory()->create(['role' => 'staff']);
        $category = Category::create(['name' => 'Drinks']);
        $product = Product::create(['name' => 'Coke', 'sku' => 'COKE-2', 'price' => 5000, 'stock' => 1, 'category_id' => $category->id]);

        $this->actingAs($cashier, 'sanctum')
            ->postJson('/api/pos', ['product_id' => $product->id, 'quantity' => 5])
            ->assertJsonValidationErrors('quantity');

        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_unauthenticated_api_requests_are_rejected(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();
        $this->getJson('/api/users')->assertUnauthorized();
    }

    public function test_staff_cannot_access_admin_api_endpoints(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff, 'sanctum');

        $this->getJson('/api/users')->assertForbidden();
        $this->getJson('/api/reports')->assertForbidden();
        $this->getJson('/api/expenses')->assertForbidden();
    }

    public function test_admin_can_void_a_sale_via_api(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Drinks']);
        $product = Product::create(['name' => 'Coke', 'sku' => 'COKE-3', 'price' => 5000, 'stock' => 10, 'category_id' => $category->id]);
        $sale = $product->sales()->create(['quantity' => 2, 'total_price' => 10000, 'sale_date' => now(), 'cashier_name' => 'fetty']);
        $product->decrement('stock', 2);

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/sales/{$sale->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Sale voided successfully!');

        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
        $this->assertDatabaseHas('stock_log', ['product_id' => $product->id, 'change_qty' => 2]);
        $this->assertSame(10, $product->fresh()->stock);
    }
}
