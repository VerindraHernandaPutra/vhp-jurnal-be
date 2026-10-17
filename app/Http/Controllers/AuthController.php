<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function csrf(Request $request)
    {
        // Hitting /sanctum/csrf-cookie sets the XSRF-TOKEN cookie automatically.
        return response()->noContent();
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user); // auto-login after register
        $request->session()->regenerate();

        return (new UserResource($user))
            ->additional(['message' => 'Registered successfully']);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = (bool) ($request->boolean('remember'));

        if (! Auth::attempt($credentials, $remember)) {
            return response()->json([
                'message' => 'Invalid credentials.',
                'errors'  => ['email' => ['The provided credentials are incorrect.']],
            ], 422);
        }

        $request->session()->regenerate();

        return (new UserResource($request->user()))
            ->additional(['message' => 'Logged in']);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }
}
