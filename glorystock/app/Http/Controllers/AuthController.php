<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Legacy login.php (GET): show the sign-in form.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Legacy login.php (POST): authenticate username + password + role.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:admin,staff'],
        ]);

        // Legacy behaviour: the selected role must match the account's role.
        if (Auth::attempt(
            ['username' => $credentials['username'], 'password' => $credentials['password'], 'role' => $credentials['role']],
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('username', 'role'))
            ->withErrors(['username' => 'Invalid username, password, or role selection.']);
    }

    /**
     * Legacy register.php (GET): show the registration form.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Legacy register.php (POST): create a staff or admin account.
     * Admin registration requires the legacy security key.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,staff'],
            'admin_key' => ['nullable', 'string'],
        ]);

        if ($validated['role'] === 'admin' && ($validated['admin_key'] ?? '') !== config('glorystock.admin_key')) {
            return back()->withInput($request->only('username', 'role'))
                ->withErrors(['admin_key' => '❌ Invalid Admin Security Key!']);
        }

        $user = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Account created successfully!');
    }

    /**
     * Legacy logout.php.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Legacy access_denied.php.
     */
    public function accessDenied(): View
    {
        return view('auth.access-denied');
    }
}
