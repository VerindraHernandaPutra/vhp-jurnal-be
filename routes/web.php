<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/sanity', fn () => response()->json(['ok' => true]));

// Sanctum CSRF cookie (needed for SPA)
Route::get('/sanctum/csrf-cookie', [AuthController::class, 'csrf'])->name('csrf');

// --- Public Auth ---
Route::post('/login', [AuthController::class, 'login'])->name('login');

// --- Authenticated Users ---
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // --- Admin-only routes ---
    Route::middleware(function ($request, $next) {
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    })->group(function () {
        Route::get('/admin/users', [UserController::class, 'index']);
        Route::post('/admin/users', [UserController::class, 'store']);
    });
});
