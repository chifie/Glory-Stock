<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Legacy registration flow (from showRegister): username/password/role,
     * with the GLORYSTOCK_ADMIN_KEY secret required for admin accounts.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', Password::min(6)],
            'role' => ['required', 'in:staff,admin'],
            'admin_key' => ['nullable', 'string'],
        ]);

        if ($data['role'] === 'admin') {
            $secret = (string) config('glorystock.admin_key');
            if ($data['admin_key'] !== $secret) {
                throw ValidationException::withMessages([
                    'admin_key' => ['Invalid admin secret key.'],
                ]);
            }
        }

        $user = User::create([
            'username' => $data['username'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        return response()->json([
            'token' => $user->createToken('spa')->plainTextToken,
            'user' => ['id' => $user->id, 'username' => $user->username, 'role' => $user->role],
        ], 201);
    }

    /**
     * Legacy login flow (from showLogin): username + password + chosen role.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:staff,admin'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Wrong username or password.'],
            ]);
        }

        if ($user->role !== $credentials['role']) {
            throw ValidationException::withMessages([
                'role' => ["This account is registered as a {$user->role}. Choose that role to sign in."],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('spa')->plainTextToken,
            'user' => ['id' => $user->id, 'username' => $user->username, 'role' => $user->role],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json(['user' => ['id' => $user->id, 'username' => $user->username, 'role' => $user->role]]);
    }
}
