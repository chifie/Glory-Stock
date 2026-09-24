<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('pos.index'))->assertRedirect(route('login'));
    }

    public function test_cashier_can_view_pos_terminal(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user)
            ->get(route('pos.index'))
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_cashier_can_process_a_sale(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 5000, 'stock' => 10]);

        $response = $this->actingAs($user)->post(route('pos.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('sales', [
            'product_id' => $product->id,
            'quantity' => 2,
            'total_price' => 10000,
            'cashier_name' => $user->username,
        ]);

        $this->assertSame(8, $product->fresh()->stock);

        $this->assertDatabaseHas('stock_log', [
            'product_id' => $product->id,
            'change_qty' => -2,
        ]);
    }

    public function test_sale_is_rejected_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1]);

        $this->actingAs($user)
            ->from(route('pos.index'))
            ->post(route('pos.store'), [
                'product_id' => $product->id,
                'quantity' => 5,
            ])
            ->assertRedirect(route('pos.index'))
            ->assertSessionHasErrors('quantity');

        $this->assertSame(1, $product->fresh()->stock);
        $this->assertSame(0, Sale::count());
    }
}
