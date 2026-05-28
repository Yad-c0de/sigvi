<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rutas para invitados
Route::middleware('guest')->group(function () {
    // LOGIN → vista Blade normal (tu diseño personalizado)
    Route::view('login', 'auth.login')->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Las demás páginas de autenticación siguen con Volt (como antes)
    Volt::route('register', 'pages.auth.register')->name('register');
    Volt::route('forgot-password', 'pages.auth.forgot-password')->name('password.request');
    Volt::route('reset-password/{token}', 'pages.auth.reset-password')->name('password.reset');
});

// Rutas para usuarios autenticados
Route::middleware('auth')->group(function () {
    // Cerrar sesión
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Verificar email (Volt)
    Volt::route('verify-email', 'pages.auth.verify-email')->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Confirmar contraseña (Volt)
    Volt::route('confirm-password', 'pages.auth.confirm-password')->name('password.confirm');
});
