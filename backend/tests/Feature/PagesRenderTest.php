<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_render_main_pages(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->get(route('dashboard'))->assertOk();
        $this->actingAs($staff)->get(route('sales.history'))->assertOk();
        $this->actingAs($staff)->get(route('stock.log'))->assertOk();
        $this->actingAs($staff)->get(route('daily.close'))->assertOk();
        $this->actingAs($staff)->get(route('reorder'))->assertOk();
    }

    public function test_admin_can_render_admin_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('reports'))->assertOk();
        $this->actingAs($admin)->get(route('expenses.index'))->assertOk();
        $this->actingAs($admin)->get(route('categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('users.index'))->assertOk();
        $this->actingAs($admin)->get(route('products.create'))->assertOk();
    }
}
