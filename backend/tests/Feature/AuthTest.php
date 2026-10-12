<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('GLORYSTOCK');
    }

    public function test_users_can_login_with_matching_role(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
            'role' => 'staff',
        ]);

        $this->post(route('login.attempt'), [
            'username' => $user->username,
            'password' => 'secret123',
            'role' => 'staff',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_when_role_does_not_match(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
            'role' => 'staff',
        ]);

        $this->post(route('login.attempt'), [
            'username' => $user->username,
            'password' => 'secret123',
            'role' => 'admin',
        ])->assertSessionHasErrors();

        $this->assertGuest();
    }

    public function test_admin_registration_requires_security_key(): void
    {
        $this->post(route('register.store'), [
            'username' => 'newadmin',
            'password' => 'secret123',
            'role' => 'admin',
            'admin_key' => 'wrong-key',
        ])->assertSessionHasErrors('admin_key');

        $this->assertDatabaseMissing('users', ['username' => 'newadmin']);
    }

    public function test_admin_registration_succeeds_with_valid_key(): void
    {
        $this->post(route('register.store'), [
            'username' => 'newadmin',
            'password' => 'secret123',
            'role' => 'admin',
            'admin_key' => config('glorystock.admin_key'),
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'username' => 'newadmin',
            'role' => 'admin',
        ]);
    }
}
